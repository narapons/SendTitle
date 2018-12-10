<?php

namespace SendTitle;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\level;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class main extends PluginBase implements Listener {

    /** @var array */
    private $titlePoses = [];

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("SendTitleを読み込みました");
        $this->Config = new Config($this->getDataFolder() ."Config.yml", Config::YAML);
        $this->titlePoses = $this->Config->getAll();
    }
    
    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $worldName = $player->getLevel()->getName();
        if (!empty($this->titlePoses)){
            foreach($this->titlePoses as $titleName => $value){
                $x = (float) $value["x"];
                $y = (float) $value["t"];
                $z = (float) $value["z"];
                $configWorldName = (string) $value["world"];
                $mainTitle = (string) $value["MainTitle"];
                $subTitle = (string) $value["SubTitle"];
            }
            $px = (float) $player->getFloorX();
            $py = (float) $player->getFloorY();
            $pz = (float) $player->getFloorZ();
            if ($x === $px && $y === $py && $z === $pz && $worldName === $configWorldName){
                $player->addTitle($mainTitle, $subTitle, "20", "20", "20");
            }
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if ($sender instanceof Player){
            switch($command->getName()){
                    case "addtitle":
                    if(!$sender->isOp()){
                        $sender->sendMessage("§cこのコマンドを実行する権限がありません");
                        return true;
                    }
                    if(!isset($args[0], $args[1], $args[2])){
                        $sender->sendMessage("使い方 : /addtitle <任意のタイトル名> <タイトル> <Subタイトル>");
                    }else{
                        $x = $sender->getFloorX();
                        $y = $sender->getFloorY();
                        $z = $sender->getFloorZ();
                        $worldName = $sender->getLevel()->getName();
                        if(!array_key_exists($args[0], $this->titlePoses)){
                            $this->addTitle($args[0], $args[1], $args[2], $x, $y, $z, $worldName);
                            $this->Config->save();
                            $sender->sendMessage("{$args[0]}を追加しました");
                        }else{
                            $sender->sendMessage("{$args[0]}は追加済みです");
                        }
                    }
                    return true;
            
                    case "deltitle":
                    if(!$sender->isOp()){
                        $sender->sendMessage("§cこのコマンドを実行する権限がありません");
                        return true;
                    }
                    if(!isset($args[0])){
                        $sender->sendMessage("使い方 : /deltitle <任意のタイトル名>");
                    }else{
                        if(array_key_exists($args[0], $this->titlePoses)){
				$this->Config->remove($args[0]);
				$this->Config->save();
				$this->Config->reload();
                            $sender->sendMessage("{$args[0]}を削除しました");
                        }else{
                            $sender->sendMessage("{$args[0]}は追加されていません");
                        }
                    }
                    return true;
                    
                    case "listtitle":
                    if(!$sender->isOp()){
                        $sender->sendMessage("§cこのコマンドを実行する権限がありません");
                        return true;
                    }
                    $sender->sendMessage("§l§9〜タイトル一覧〜");
                    foreach($this->Config->getAll() as $key=>$value){
			                     $sender->sendMessage("{$key}");
                    }
                    return true;
            }
        }
    return true;
    }
    
    public function addTitle(string $titleName, string $MainTitle, string $SubTitle, string $x, string $y, string $z, string $worldName){
        $data = [
            "x" => $x,
            "t" => $y,
            "z" => $z,
            "world" => $worldName,
            "MainTitle" => $MainTitle,
            "SubTitle" => $SubTitle
        ];
        $this->titlePoses[$titleName] = $data;
        $this->Config->set($titleName, $data);
        $this->Config->save();
    }
}
