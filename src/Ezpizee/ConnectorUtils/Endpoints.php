<?php

namespace Ezpizee\ConnectorUtils;

class Endpoints
{
    const INSTALL = '/api/install';
    const LOGIN = '/api/user/login';
    const GET_TOKEN = '/api/user/token';
    const LOGOUT = "/api/user/logout";
    const REGISTER = "/api/user/add";
    const ACTIVATE = "/api/user/activate/{id}";
    const PREDRFIND_COUNTRY = "/api/global-property/predefined/countries";
    const PAYMENT_SERVICE = "/api/payment/list";
    const PAYMENT_SERVICE_URL = "/api/payment/{payment_type}/{payment_id}/processing/url/{method_type}/{cart_id}";
    const PAYMENT_CREATE_ORDER = "/api/payment/{payment_type}/{payment_id}/{method_type}/order/create/{cart_id}";
    const PAYMENT_CAPTURE_ORDER = "/api/payment/{payment_type}/{payment_id}/{method_type}/order/capture/{order_payment_id}/{cart_id}";
    const PAYMENT_VERIFY = "/api/payment/check/byorder/{cart_id}";
    const PRODUCT_ITEM = "/api/pim/product/item/{id}";
    const PRODUCT_PRICE = "/api/pim/product/price/{id}";
    const PRODUCT_LIST = "/api/pim/product/list/bychannel/web/numperpage/{number_per_page}/start/{start}";
    const CART_CREATE = "/api/cart/create/{store_id}";
    const CART_UPDATE = "/api/cart/update/{id}";
    const CART_EMPTY = "/api/cart/empty/{cart_id}";
    const CART_CONTENT = "/api/cart/content/by/id/{id}";
    const CART_ADD_ITEM = "/api/cart/add/items/{cart_id}";
    const CART_REMOVE_ITEM = "/api/cart/remove/items/{cart_id}";
    const CART_INCREASE_QUANTITY = "/api/cart/increase/quantity/{cart_id}/{product_id}/{increase_by}";
    const CART_DECCREASE_QUANTITY = "/api/cart/decrease/quantity/{cart_id}/{product_id}/{decrease_by}";
    const CART_APPLY_COUPON = "/api/cart/apply/coupon/{cart_id}";
    const CART_CHECKOUT = "/api/cart/checkout/{cart_id}";
    const CART_REMOVE_COUPON = "/api/cart/remove/offer-type/coupon/{cart_id}";
    const CUSTOMER_PROFILE = "/api/storefront/customer/item/{id}";
    const CUSTOMER_REGISTER = '/api/storefront/customer/register';
    const CUSTOMER_UPDATE = '/api/storefront/customer/update/{id}';
    const CUSTOEMR_LOGIN = '/api/storefront/customer/login';


    public static function endpoint(string $str, array $params = []): string
    {
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $str = str_replace('{' . $k . '}', $v, $str);
            }
        }
        return $str;
    }
}
