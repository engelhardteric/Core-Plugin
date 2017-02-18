<?php

namespace Core;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\particle\FlameParticle;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityTeleportEvent;

class Main extends PluginBase implements Listener {
	
	public $prefix = TextFormat::GRAY."[".TextFormat::AQUA."Core".TextFormat::GRAY."] ";

	public function onEnable(){
		$this->getLogger()->info($this->prefix.C::GREEN."Aktiviert");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new Scoreboard($this), 20);
    }

	public function MainItems(Player $player){
		$player->getInventory()->clearAll();
		$player->getInventory()->setItem(0, Item::get(345)->setCustomName(C::BOLD.C::GOLD."Teleporter"));
        $player->getInventory()->setItem(2, Item::get(339)->setCustomName(C::BOLD.C::GOLD."Info"));
        $player->getInventory()->setItem(6, Item::get(280)->setCustomName("§eSpieler Verstecken"));
        $player->removeAllEffects();
	    $player->getPlayer()->setHealth(20);
	        $player->getPlayer()->setFood(20);

	}
	
        public function killm(PlayerDeathEvent $event)
        {
            $event->setDeathMessage("");
        }

        

        public function TeleportItems(Player $player){          //Teleport
		$player->getInventory()->clearAll();
        $player->getInventory()->setItem(3, Item::get(280)->setCustomName(C::BOLD.C::BLUE."Light Wars"));
        $player->getInventory()->setItem(8, Item::get(341)->setCustomName(C::BOLD.C::RED."Bäääck"));
        $player->getInventory()->setItem(0, Item::get(267)->setCustomName(C::BOLD.C::RED."QSG"));
        $player->getInventory()->setItem(2, Item::get(19)->setCustomName(C::BOLD.C::AQUA."LSW"));
        $player->removeAllEffects();
        $player->getPlayer()->setHealth(20);
        $player->getPlayer()->setFood(20);
	}


	public function onJoin(PlayerJoinEvent $event){         //OnJoin
		$player = $event->getPlayer();
		$name = $player->getName();
	    $ds = $this->getServer()->getDefaultLevel()->getSafeSpawn();
        $player->setGamemode(0);
        $player->teleport($ds);
        $event->setJoinMessage("");
		$this->MainItems($player);
		$player->setGamemode(0);
		if($player->isOP()){
			$event->setJoinMessage(C::RED.$name.C::AQUA." hat das Spiel betreten");
		}
	}
	
	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
	    $event->setQuitMessage("");
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$name = $sender->getName();
		switch ($cmd->getName()){
			case "Info":
			if(!empty($args[0])){
				if($args[0] == "team"){
					$sender->sendMessage($this->prefix . " §aDu kannst dich bei uns bewerben > www.bamcraftpe.de");
					return true;
				}
				if($args[0] == "youtuber"){
					$sender->sendMessage($this->prefix . "§aDen YouTuber Rang gibt es ab 200 Subs!");
					return true;
				}
				if($args[0] == "ts"){
					$sender->sendMessage($this->prefix . "§ats.bamcraftpe.de");
					return true;
				}
			}else{
				$sender->sendMessage($this->prefix. "§a/info team|youtuber|ts");
				return true;
			}
            case "Hub":
			if($sender instanceof Player){
				$ds = $this->getServer()->getDefaultLevel()->getSafeSpawn();
				$sender->teleport($ds);
				$sender->sendMessage($this->prefix . "§aWillkommen am Spawn");
				$this->MainItems($sender);
				$sender->setHealth(20);
				$sender->setFood(20);
				return true;
			}
		}
	}
	
    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $item = $player->getInventory()->getItemInHand();
        $itemid = $item->getID();
        $block = $event->getBlock();


        if($item->getName() == C::BOLD.C::GOLD."Teleporter"){
            $this->TeleportItems($player);
        }

        elseif ($item->getName() == C::BOLD . C::GOLD . "Info"){
            $player->sendMessage($this->prefix . "§abenutze /info team|youtuber|ts");
        }

        elseif ($item->getName() == C::BOLD . C::RED . "Bäääck"){
            $this->MainItems($player);
        }


        elseif ($item->getName() == C::BOLD . C::BLUE . "Light Wars"){
			$this->MainItems($player);
            $x = 232;
            $y = 4;
            $z = 270;
            $player->teleport(new Vector3($x, $y, $z));
        }

        elseif ($item->getName() == C::BOLD.C::RED."QSG"){
       $this->MainItems($player);
            $x = 259;
            $y = 4;
            $z = 248;
            $player->teleport(new Vector3($x, $y, $z));
        }
        
        elseif ($item->getName() == C::BOLD.C::AQUA."LSW"){
       $this->MainItems($player);
            $x = 266;
            $y = 4;
            $z = 295;
            $player->teleport(new Vector3($x, $y, $z));
        }

        elseif ($item->getCustomName() == "§eSpieler Verstecken") {
            $player->getInventory()->remove(Item::get(280)->setCustomName("§eSpieler Verstecken"));
            $player->getInventory()->setItem(6, Item::get(369)->setCustomName("§eSpieler Anzeigen"));
            $player->sendMessage($this->prefix . "§aAlle Spieler sind nun unsichtbar!");
            $this->hideall[] = $player;
            foreach ($this->getServer()->getOnlinePlayers() as $p2) {
                $player->hideplayer($p2);
            }
        }

        elseif ($item->getCustomName() == "§eSpieler Anzeigen"){
            $player->getInventory()->remove(Item::get(369)->setCustomName("§eSpieler Anzeigen"));
            $player->getInventory()->setItem(6, Item::get(280)->setCustomName("§eSpieler Verstecken"));
            $player->sendMessage($this->prefix . "§aAlle Spieler sind nun wieder Sichtbar!");
            unset($this->hideall[array_search($player, $this->hideall)]);
            foreach ($this->getServer()->getOnlinePlayers() as $p2) {
                $player->showplayer($p2);
            }
        }
	}
	
	public function onBlockBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if($player->isOP()){
			$event->setCancelled(false);
		}else{
			$event->setCancelled(true);
			$player->sendMessage($this->prefix.TextFormat::RED." Du kannst hier nichts kaputt machen".C::GRAY."!");
		}
	}
	
	public function onBlockPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if($player->isOP()){
			$event->setCancelled(false);
		}else{
			$event->setCancelled(true);
			$player->sendMessage($this->prefix.TextFormat::RED." Du kannst hier nichts platzieren".C::GRAY."!");
		}
	}
	
	public function onItemHeld(PlayerItemHeldEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$item = $player->getInventory()->getItemInHand()->getID();
		switch($item){
			case 10:
			$player->getInventory()->setItemInHand(Item::get(Item::AIR, 0, 0));
			$player->sendMessage($this->prefix.TextFormat::RED." Du darfst keine lava benutzen");
			$this->getLogger()->critical($name." versucht lava zu usen");
			return true;
			case 11:
			$player->getInventory()->setItemInHand(Item::get(Item::AIR, 0, 0));
			$player->sendMessage($this->prefix.TextFormat::RED." Du darfst keine lava benutzen");
			$this->getLogger()->critical($name." versucht lava zu usen");
			return true;
			case 46:
			$player->getInventory()->setItemInHand(Item::get(Item::AIR, 0, 0));
			$player->sendMessage($this->prefix.TextFormat::RED." Du darfst kein Tnt benutzen");
			$this->getLogger()->critical($name." versucht tnt zu usen");
			return true;
			case 325:
			$player->getInventory()->setItemInHand(Item::get(Item::AIR, 0, 0));
			$player->sendMessage($this->prefix.TextFormat::RED." Du darfst kein eimer benutzen");
			$this->getLogger()->critical($name." versucht bucket zu usen");
			return true;
		}
	}
}
