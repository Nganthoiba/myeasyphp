<?php
//#Point to be noted: Please don't edit or remove anything below enclosed by //
///////////////////////////////// DON'T EDIT OR REMOVE ///////////////////////////////
//                                                                                  //
//PATH CONFIGURATION FILES                                                          //
define("ROOT",dirname(__DIR__));                                                    //
define("DS", DIRECTORY_SEPARATOR);                                                //
//define("DS", '/');                                                                  //
                                                                                    //
define("VENDOR_PATH", ROOT.DS."Vendor".DS);                                         //
define("CONFIG_PATH", ROOT.DS."Config".DS);                                         //
define("LIBS_PATH", ROOT.DS."Libs".DS);                                             //
define("MODELS_PATH", ROOT.DS."Models".DS);                                         //
define("VIEWS_PATH", ROOT.DS."Views".DS);                                           //
define('ENTITY_PATH', MODELS_PATH.DS.'Entities'.DS);//Path for entities             //
                                                                                    //
//NAMESPACES                                                                        //
define('ENTITY_NAMESPACE','MyEasyPHP\\Models\\Entities\\');                         //
define('CONTROLLER_NAMESPACE','MyEasyPHP\\Controllers\\');                          //
                                                                                    //
//////////////////////////////////////////////////////////////////////////////////////

//////////////////// You can define your new constants below: ////////////////////////