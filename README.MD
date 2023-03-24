# asyncMySQL

Plugin for PMMP 4, for working with the database and models (for saving player data).

# PHP 8.2+

# Usage:

### Creating Model (PlayerModel):

```php
<?php

namespace my\plugin\models;

use celis\async\mysql\model\PlayerModel;

/**
 * @property string userdata
 */
class TestModel extends PlayerModel {

	public static string $table = "test"; // set up the table that we will use
	protected array $fillable = ["username", "userdata"]; // the data that we will write to the table
}
```

We use property in order to conveniently access this data in the model.

### Usage in plugin:

```php
<?php

namespace my\plugin;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use my\plugin\models\TestModel;

use celis\async\mysql\storage\Storage;

class MyPlugin extends PluginBase implements Listener {

	protected function onLoad() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		
		$model = new TestModel();
		$model->setPlayer($player);
		$model->fetch(); // loading user data, if they exist
		
		$model->userdata = "foo"; // setting data for example
		
		$id = Storage::getInstance()->addModel($model); // caching model
		
		print ($id);
	}
	
	public function onQuit(PlayerQuitEvent): void {
		$player = $event->getPlayer();
		
		/** @var TestModel $model */
		$model = Storage::getInstance()->getModel(1); // use id
		
		print_r($model->userdata); // returns 'foo'
		
		$model->userdata = "bar";
		
		$model->save(); //saving model
		
		Storage::getInstance()->removeModel(1); // use id
	}
}
```

You can create your own Storage class to store models for your tasks.
