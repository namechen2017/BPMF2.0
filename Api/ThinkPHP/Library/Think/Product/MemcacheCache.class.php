<?php
namespace Think\Product;
/**
 * 缓存类
 * memcache缓存写入的地方，统一管理，避免多处生成缓存，以及重复缓存和代码重复的问题产生
 * 采用驼峰命名法来对缓存进行保存，缓存名采用下划线加表名的方法来映射
 */

class MemcacheCache {

        
        //缓存路径访问表
	public function authConfField(){
           $cache = \Think\Cache::getInstance('Memcache');
           $data = $cache->get('auth_conf_field');   
           if(!$data){
              $data = M('auth_conf')->getField('request_address,uid');
              $cache->set('auth_conf_field', $data);   
           }
           return  $data; 
	}
        
        //缓存路径权限表
        public function authRuleField(){
           $cache = \Think\Cache::getInstance('Memcache'); 
           $data = $cache->get('auth_rule_field');   
           if(!$data){
              $data = M('auth_rule')->where(array('status'=>1))->getField('id,auth_address,name,enabled,p_id,nav_id');
              $cache->set('auth_rule_field', $data);   
           }
           return  $data;  
        }
        
        
        //缓存权限节点表
        public function authRoleField(){
           $cache = \Think\Cache::getInstance('Memcache'); 
           $data = $cache->get('auth_role_field');   
           if(!$data){
              $data = $data =  M('auth_role')->where(array('status'=>1))->getField('id,permissions,name,enabled');
              $cache->set('auth_role_field', $data);   
           }
           return  $data;    
        }
        
        //缓存节点用户ID表
        public function authRoleUserField(){
            
           $cache = \Think\Cache::getInstance('Memcache'); 
           $data = $cache->get('auth_role_user_field');   
           if(!$data){
              $data = $data =  M('auth_role_user')->getField('user_id,role_id');
              $cache->set('auth_role_user_field', $data);   
           }
           return  $data;      
        }
        
        
        //缓存数据字典
        public function enumGroup(){
            
           $cache = \Think\Cache::getInstance('Memcache'); 
           $data = $cache->get('enum_group');   
           if(!$data){
              $res =  M('enum_dictionary')->where(array('enabled'=>1))->order("sort desc")->field('id,value,name,key_name,key_value,remark')->select();
             
              $data = array();
              foreach($res as $key =>$val){ 
               $data[$val['value']]['name'] = $val['name'];
               $data[$val['value']]['value'] = $val['value'];
               $data[$val['value']]['detail'][] = $val;
             }
              $cache->set('enum_group', $data);   
           }
           return  $data; 
        }
        
        
        
        //数据的清除，当你要修改了某个东西的时候，直接删除就好了，后续会自动调用缓存的
        public function flushCache($key){
            
            
           $cache = \Think\Cache::getInstance('Memcache'); 
           
           $cache->rm($key); 
        }
        
        
}