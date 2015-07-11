<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

class Weather_cell extends Cell_Controller {

  /* render_cell ('weather_cell', 'icon', array ()); */
  public function _cache_icon ($weather) {
    return array ('time' => 60 * 60, 'key' => $weather->id);
  }
  public function icon ($weather) {
    CallLog::create (array ('weather_id' => $weather->id));

    if (($data = download_web_file ('http://api.openweathermap.org/data/2.5/weather?lat=' . $weather->latitude . '&lon=' . $weather->longitude . '')) && ($data = json_decode ($data))) {
      if (isset ($data->weather[0]) && $data->weather[0]->icon && isset ($data->main->temp)) {
        $weather->temperature = $data->main->temp;
        $weather->save ();
        $file_path = array ('resource', 'image', 'map', $data->weather[0]->icon . '.png');
        
        if (file_exists (implode (DIRECTORY_SEPARATOR, $file_path)))
          return array (
              'temperature' => round ($weather->temperature - 273.15),
              'icon' => base_url ($file_path)
            );
        else
          return '';
      }
    }
    return '';
  }
}