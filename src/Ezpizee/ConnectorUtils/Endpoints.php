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
    const PRODUCT_ITEM = "/api/pim/product/item/{id}";
    const PRODUCT_PRICE = "/api/pim/product/price/{id}";
    const CART_CREATE = "/api/cart/create/{store_id}";
    const CART_CONTENT = "/api/cart/content/by/id/{id}";
    const CART_ADD_ITEM = "/api/cart/add/items/{cart_id}";
    const CART_APPLY_COUPON = "/api/cart/apply/coupon/{cart_id}";
    const CART_CHECKOUT = "/api/cart/checkout/{cart_id}";


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
