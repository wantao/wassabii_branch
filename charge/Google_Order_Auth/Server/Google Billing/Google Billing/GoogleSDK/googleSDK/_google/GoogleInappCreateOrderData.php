<?php
  
Doo::loadClass('api/UserAuthData');
  
class GoogleInappCreateOrderData extends UserAuthData
{
    function __construct()
    {
        parent::__construct();
        $validationRules = $this->ValidationRuleManager->getValidationRules(); //繼承機制
        $validationRules['package_name'] = array(
            "data-val"=>"true"
            ,"data-val-required"=>'required'
            ,"data-val-length-max"=>"512"
            ,"data-val-length"=>"length"
        );
        $validationRules['product_id'] = array(
            "data-val"=>"true"
            ,"data-val-required"=>'required'
            ,"data-val-length-max"=>"512"
            ,"data-val-length"=>"length"
        );
        $validationRules['price'] = array(
            "data-val"=>"true"
            ,"data-val-required"=>'required'
            ,"data-val-length-max"=>"50"
            ,"data-val-length"=>"length"
        );
        $this->ValidationRuleManager = new ValidationRuleManager($this,$validationRules);
    }
    
    public $package_name;
    public $product_id;
    public $price;
}
