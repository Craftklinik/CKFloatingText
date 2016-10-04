<?php

	namespace Craftklinik;

	use pocketmine\plugin\PluginBase;
	use pocketmine\event\Listener;
	use pocketmine\utils\Config;
	use pocketmine\utils\TextFormat;
	use pocketmine\level\particle\FloatingTextParticle;
	use pocketmine\math\Vector3;
	use pocketmine\event\player\PlayerJoinEvent;
	use pocketmine\command\Command;
	use pocketmine\command\CommandSender;
	use pocketmine\Player;
	use pocketmine\Server;

	class CKFloatingText extends PluginBase implements Listener {
		private $config;

		public function onEnable() {
			if(!is_dir($this->getDataFolder()))
				@mkdir($this->getDataFolder());
			$this->config = (new Config($this->getDataFolder().'FloatingText_DB.yml', Config::YAML))->getAll();
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
			$this->getLogger()->info(TextFormat::GREEN."Plugin geladen!");
			$this->getLogger()->info(TextFormat::RED."Plugin by Craftklinik!");
			$this->getLogger()->info(TextFormat::AQUA."Craftklinik on Twitter: Craftklinik");
		}

		public function onDisable() {
			$cfg = new Config($this->getDataFolder().'FloatingText_DB.yml', Config::YAML);
			$cfg->setAll($this->config);
			$cfg->save();
			$this->getLogger()->info(TextFormat::GREEN."Plugin ungeladen!!");
		}

		public function onJoin(PlayerJoinEvent $event) {
			$player = $event->getPlayer();
			if($player instanceof Player) {
				foreach($this->config as $coord => $text) {
					$coord = explode(':', $coord);
					$x = $coord[0];
					$y = $coord[1];
					$z = $coord[2];
					$br = explode("\\n", $text);
					$text = "";
					foreach($br as $line) 
						$text .= $line."\n";
					$player->getLevel()->addParticle(new FloatingTextParticle(new Vector3($x, $y, $z), '', $text), array($player));
				}
			}
		}

		public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
			if($sender instanceof Player) {
				if($command->getName() == 'addtag') {
					if(count($args) > 0) {
						$text = "";
						foreach($args as $word)
							$text .= "$word ";
						$text = trim($text);
						$x = $sender->getX();
						$y = $sender->getY() + 2;
						$z = $sender->getZ();
						$this->config[$x.':'.$y.':'.$z] = $text;
						$cfg = new Config($this->getDataFolder().'FloatingText_DB.yml', Config::YAML);
						$cfg->setAll($this->config);
						$cfg->save();
						$br = explode("\\n", $text);
						$text = "";
						foreach($br as $line)
							$text .= $line."\n";
						$sender->getLevel()->addParticle(new FloatingTextParticle(new Vector3($x, $y, $z), '', $text));
						$sender->sendMessage(TextFormat::AQUA."§c[§aCKFloatingText§c] §r§e Der Text wurde auf den Koordinaten: $x $y $z gesetzt!");
					} else $sender->sendMessage(TextFormat::RED."Benutze: /addtag <text>");
				}
			} else $sender->sendMessage(TextFormat::RED."Only for players");
		}

	}

?>