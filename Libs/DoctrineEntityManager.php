<?php
namespace MyEasyPHP\Libs;

use MyEasyPHP\Libs\Config;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\Common\EventManager;
/**
 * Description of DoctrineEntityManager
 * 
 * To generate Entity Manager for Doctrine
 * @author Nganthoiba
 */
class DoctrineEntityManager extends EntityManager{
    /***Method to get Entity Manager for Doctrine ****/
    public static function getEntityManager(): DoctrineEntityManager{
        //$isDevMode = Config::get('development_mode');
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $db_config = $_ENV;
        /*Database Configuration set up*/
        $db_param = array(
            'dbname' => $db_config['DB_NAME'],
            'user' => $db_config['DB_USERNAME'],
            'password' => $db_config['DB_PASSWORD'],
            'host' => $db_config['DB_HOST'],
            'driver' => "pdo_".$db_config['DB_DRIVER'],
            'port'=>$db_config['DB_PORT']??""
        );
        $dev_mode = isset($_ENV['DEVELOPMENT_MODE'])&&$_ENV['DEVELOPMENT_MODE']==='ON'?true:false;
        $config = Setup::createAnnotationMetadataConfiguration(
                array(ENTITY_PATH), 
                $dev_mode, 
                $proxyDir, 
                $cache, 
                $useSimpleAnnotationReader
                );
        return self::create($db_param, $config);
    }
    
    public static function create($connection, Configuration $config, EventManager $eventManager = null): DoctrineEntityManager {
        if ( ! $config->getMetadataDriverImpl()) {
            throw ORMException::missingMappingDriverImpl();
        }

        $connection = static::createConnection($connection, $config, $eventManager);

        return new DoctrineEntityManager($connection, $config, $connection->getEventManager());
    }
    
    public function find($className, $id, $lockMode = null, $lockVersion = null) {
        if(is_object($className)){
            $className = get_class($className);
        }
        else if(is_string($className) && strpos($className,ENTITY_NAMESPACE)===false){
            $className = ENTITY_NAMESPACE.$className;
        }
        return parent::find($className, $id, $lockMode, $lockVersion);
    }
    public function getRepository($entityName) {
        if(is_object($entityName)){
            $entityName = get_class($entityName);
        }
        else if(is_string($entityName) && strpos($entityName,ENTITY_NAMESPACE)===false){
            $entityName = ENTITY_NAMESPACE.$entityName;
        }
        return parent::getRepository($entityName);
    }
}