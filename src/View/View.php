<?php

namespace App\View;

use Jenssegers\Blade\Blade;
use App\Application\Settings\SettingsInterface;

class View {

  private $blade;

  public function __construct(SettingsInterface $settings){
    $s = $settings->get('html');
    $this->blade = new Blade($s['path'], $s['cache']);
  }

  public function render(string $template, array $data = []){
    return $this->blade->make($template, $data)->render();
  }
}
