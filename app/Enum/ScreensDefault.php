<?php


namespace App\Enum;


class ScreensDefault
{
    //Screen configuration for create role which default permision
    public const USER = array("MAP", "PRICE", "CERTIFICATE_ASSET", "CERTIFICATE_BRIEF", "PRE_CERTIFICATE", "PAYMENT");
    public const ADMIN_SCREENS = array("DASHBOARD", "USER", "ROLE", "MAP", "PRICE", "CATEGORY", "CUSTOMER", "CERTIFICATE_ASSET", "CERTIFICATE_BRIEF", "PRE_CERTIFICATE", "PAYMENT");
    public const ROOT_ADMIN_SCREENS = array("DASHBOARD", "USER", "ROLE", "MAP", "PRICE", "CATEGORY", "PROPERTIES", "CUSTOMER", "CERTIFICATE_ASSET", "CERTIFICATE_BRIEF", "PRE_CERTIFICATE", "PAYMENT");

    //All screens can be configured by the user
    public const ALL_SCREENS = array("DASHBOARD", "USER", "ROLE", "MAP", "PRICE", "CATEGORY", "CUSTOMER", "CERTIFICATE_ASSET", "CERTIFICATE_BRIEF", "PRE_CERTIFICATE", "ACCOUNTING");

    public const DASHBOARD_SCREEN = "DASHBOARD";
    public const USER_SCREEN = "USER";
    public const ROLE_SCREEN = "ROLE";
    public const MAP_SCREEN = "MAP";
    public const PRICE_SCREEN = "PRICE";
    public const CATEGORY_SCREEN = "CATEGORY";
    public const PROPERTIES_SCREEN = "PROPERTIES";
    public const CUSTOMER_SCREEN = "CUSTOMER";
    public const CERTIFICATE_ASSET_SCREEN = "CERTIFICATE_ASSET";
    public const CERTIFICATE_BRIEF_SCREEN = "CERTIFICATE_BRIEF";
    public const PRE_CERTIFICATE_SCREEN = "PRE_CERTIFICATE";
    public const ACCOUNTING_SCREEN = "ACCOUNTING";
}
