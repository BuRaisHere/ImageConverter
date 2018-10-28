<?php
namespace korado531m7\ImageConverter;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
//use pocketmine\utils\Config; For switching java edition and bedrock edition

class ImageConverter extends PluginBase{
    private static $workingTask = [];
    
    public function onEnable(){
        if(!extension_loaded("gd")){
            $this->getServer()->getLogger()->error('Install GD Library first');
            $this->getServer()->forceShutdown();
        }
        if(@mkdir($this->getDataFolder(), 0744, true)){
            $this->getServer()->notice('Folder \''.$this->getName().'\' has been created to plugin data folder. put image data in it.');
        }
    }
    
    public static function addTask(AsyncTask $task){
        self::$workingTask[] = $task;
    }
    
    public static function returnAllTask() : array{
        return self::$workingTask;
    }
    
    public static function removeTask(AsyncTask $task){
        $search = array_search($task,self::$workingTask);
        unset(self::$workingTask[$search]);
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $params) : bool{
        if($command->getName() == 'convert'){
            switch($params[0] ?? null){
                case 'image-list':
                    $iterator = new \RecursiveDirectoryIterator($this->getDataFolder());
                    $files = [];
                    foreach($iterator as $file){
                        $extension = $file->getExtension();
                        if($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg'){
                            $files[] = $file->getBasename();
                        }
                    }
                    $sender->sendMessage('========== Available Images =========='.PHP_EOL.implode(', ',$files).PHP_EOL.'==============================');
                break;
                
                case 'image':
                case 'i':
                    if(empty($params[1])){
                        $sender->sendMessage('/convert image <filename>');
                    }else{
                        if(file_exists($this->getDataFolder().$params[1])){
                            $sender->sendMessage('Extracting Image Data in other thread...');
                            $this->getServer()->getAsyncPool()->submitTask($task = new ExtractImageTask($this->getDataFolder().$params[1],$sender->getName()));
                            self::addTask($task);
                        }else{
                            $sender->sendMessage('Not found: '.$this->getDataFolder().$params[0]);
                        }
                    }
                    break;
                
                case 'list':
                case 'l':
                    $id = array();
                    foreach(self::$workingTask as $taskId){
                        $id[] = $taskId;
                    }
                    $sender->sendMessage('== Image Convert Async List ==');
                    if(count($id) == 0){
                        $sender->sendMessage('No tasks are running');
                    }else{
                        foreach($id as $iid){
                            $sender->sendMessage('Task No.'.$iid->getTaskId().' / Progress '.$iid->getTaskProgress().'% ('.$iid->getFilename().')');
                        }
                    }
                    $sender->sendMessage('==========================');
                break;
                
                case 'cancel':
                case 'c':
                    if(empty($params[1])){
                        $sender->sendMessage('/convert canceltask <taskid>');
                    }else{
                        if(preg_match('/[0-9]/',$params[1])){
                            $int = ((int) $params[1]);
                            $task = null;
                            foreach(self::$workingTask as $alltasks){
                                if($alltasks->getTaskId() == $int){
                                    $task = $alltasks;
                                }
                            }
                            if($task === null){
                                $sender->sendMessage('Task not found');
                            }else{
                                self::removeTask($task);
                                $this->getServer()->getAsyncPool()->removeTask($task,true);
                            }
                        }else{
                            $sender->sendMessage('Task id must be an integer');
                        }
                    }
                break;
                
                default:
                    $sender->sendMessage('==== Image Converter ====');
                    $sender->sendMessage('/convert image <filename> - Convert image into block');
                    $sender->sendMessage('/convert image-list - List of image in folder');
                    $sender->sendMessage('/convert list - Show working task list');
                    $sender->sendMessage('/convert cancel <taskid> - Cancel converting');
                    $sender->sendMessage('=========================');
                break;
            }
        }
        return true;
    }
}