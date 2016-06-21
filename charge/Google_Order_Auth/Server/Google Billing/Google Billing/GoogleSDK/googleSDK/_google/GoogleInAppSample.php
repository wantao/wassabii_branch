<?php

class GoogleInAppSample {
    
    protected function google_inapp_create_order(GoogleInappCreateOrderData $data)
    {
        $result = array('result-code'=>0,'result-message'=>'unknown error');
        
        $userInfo = $this->_check_session_and_get_user($data->user_platform_id, $data->session_key);
        
        Doo::loadClass('service/GoogleAppProductService');
        
        $gaps = new GoogleAppProductService();
        $gap = $gaps->find_by_package_name_and_product_id($data->package_name,$data->product_id);
        if (empty($gap))
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"google app product not found!");
        }
        
        $memo_array = array('package_name'=>$data->package_name,'product_id'=>$gap->product_id,'price'=>$data->price);
        
        //建立儲值訂單
        Doo::loadModel("BuyOrder");
        $bo = new BuyOrder();
        $bo->user_id = $data->user_platform_id;
        $bo->payment_id = 11; //google inapp儲點
        $bo->point = $gap->point;
        $bo->price = 0;
        $bo->transaction_datetime = date('Y-m-d H:i:s',time());
        $bo->status = 0;
        $bo->memo = json_encode($memo_array);
        $bo->insert();
        
        $buy_order_id = $bo->id;
        $developer_payload = AES::Encrypt(Doo::conf()->API_AES_ENCRYPT_KEY_HEX,$buy_order_id);  
        
        $result = array('result-code'=>200,'result-message'=>'ok'
                        ,'developer_payload'=>$developer_payload
                        );
        $this->jsonOut($result);    
    }
    
    protected function google_inapp_confirm_order(GoogleInappConfirmOrderData $data)
    {
        $result = array('result-code'=>0,'result-message'=>'unknown error');
         
        $userInfo = $this->_check_session_and_get_user($data->user_platform_id, $data->session_key);
        
        Doo::loadClass('service/GoogleAppService');
        Doo::loadClass('service/GoogleAppProductService');
        Doo::loadClass('service/BuyOrderService');
        
        $gas = new GoogleAppService();
        $ga = $gas->find_by_package_name($data->package_name);
        if (empty($ga))
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"google-app not found!");
        }
        
        $gaps = new GoogleAppProductService();
        $gap = $gaps->find_by_app_sn_and_product_id($ga->sn,$data->product_id);
        if (empty($gap))
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"google-app-product not found!");
        }
        
        //查詢該訂單是否已建立過(用google訂單號下去查)
        $bos = new BuyOrderService();
        $bo = $bos->getOrderByPaymentId_and_MpOrderId(11,$data->order_id);
        if (!empty($bo))
        {
            throw new ApiException(ApiErrorType::Forbidden_403(),"google-order-id was already exist!!");   
        }
             
        //從developer_payload解出buy_order_id   
        $buy_order_id = AES::Decrypt(Doo::conf()->API_AES_ENCRYPT_KEY_HEX,$data->developer_payload);      
        
        //找儲值訂單
        $bo = $bos->findByPrimarykey($buy_order_id);
        if (empty($bo)) //找不到對應的訂單
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"buy order not found!");  
        }
        
        //驗是否同張訂單
        $memo_array = json_decode($bo->memo,TRUE);
        $package_name = array_safe_get($memo_array, 'package_name');
        $product_id = array_safe_get($memo_array ,'product_id');
        if ($package_name != $data->package_name
            || $product_id != $data->product_id)
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"buy order not found!(package_name or product_id not match?)");        
        }
        
        //記log
        Doo::loadModel('LogApiGoogleInappPurchase');
        
        $log = new LogApiGoogleInappPurchase();
        $log->log_api_sn = $this->_api_log_sn;
        $log->buy_order_id = $buy_order_id;
        $sn = $log->insert();
        $log->sn = $sn;
        
        if ($bo->status != 0) //已給點
        {
            $log->give_point_result = 'Y';
            $log->update();
            $result = array('result-code'=>200,'result-message'=>'ok');
            $this->jsonOut($result);  
            return;        
        }
        
        //向google確認
        require_once Doo::conf()->MY_CLASS.'utils/google-api-php-client/Google_Client.php';
        require_once Doo::conf()->MY_CLASS.'utils/google-api-php-client/contrib/Google_AndroidpublisherService.php';
        
        #取出developer資料
        Doo::loadClass('service/GoogleDeveloperService');
        $gds = new GoogleDeveloperService();
        $gd = $gds->findByPrimarykey($ga->developer_id);
        if (empty($gd))
        {
            throw new ApiException(ApiErrorType::Not_Found_404(),"google developer not found!");
        }

        $client = new Google_Client();
        $client->setClientId($gd->api_client_id);
        $client->setClientSecret($gd->api_client_secret);
        $client->setAccessToken($gd->api_json_access_token);
        $service = new Google_AndroidpublisherServiceV11($client);
        $inappPurchasesResult = array();
        try #用try cache避免被google exception中斷
        {     
            $inappPurchasesResult = $service->inappPurchases->get($data->package_name,$data->product_id,$data->purchase_token);
        }
        catch(Exception $e)
        {
            $inappPurchasesResult['error'] = $e->getMessage();
        }
        
        $log->response_datetime = date('Y-m-d H:i:s');
        $log->inapp_purchase_result = json_encode($inappPurchasesResult);
        $log->update();
        
        #更新access token(google service lib會自動判斷有效性並決定是否重取)
        $new_access_token = $client->getAccessToken();
        if (!empty($new_access_token) 
            && ($new_access_token != $gd->api_json_access_token)
            )
        {
            $gds->updateAccessToken($gd->id, $new_access_token);
        }
        
        #驗證$inappPurchasesResult
        $pass_GoogleAPICheck = (!array_key_exists('error',$inappPurchasesResult)
                && array_safe_get($inappPurchasesResult,'kind', 'xx') == "androidpublisher#inappPurchase"
                && array_safe_get($inappPurchasesResult,'purchaseState', 'xx') === 0 //交易成功
                && array_safe_get($inappPurchasesResult,'consumptionState', 'xx') === 1 //耗用成功
                && array_safe_get($inappPurchasesResult,'developerPayload', 'xx') == $data->developer_payload //payload符合
            );
            
        $log->check_result = ($pass_GoogleAPICheck ? 'Y' : 'N');
        $log->update();

        if ($pass_GoogleAPICheck !== TRUE)
        {
            throw new ApiException(ApiErrorType::PaymentRequired_402(),"Google API validation FAIL!");       
        }
        
        #更新buy_order & 儲點
        Doo::loadClass('service/UserService');
        $us = new UserService();
        $balance_detail = $us->getAddWalletPointByUserId($bo->user_id, $bo->point,TRUE);
            
        $bo->status = 1;
        $memo_array['log'] = '['.$balance_detail['old'].'] to ['.$balance_detail['new'].']';
        $bo->memo = json_encode($memo_array);
        
        $bo->str_mac = $data->order_id;
        $bo->update();
        
        $log->give_point_result = 'Y';
        $log->update();
         
        $result = array('result-code'=>200,'result-message'=>'ok');
        $this->jsonOut($result);    
    }
}