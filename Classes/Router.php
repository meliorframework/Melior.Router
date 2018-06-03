<?php
/**
 * Copyright 2018 Maximilian Schmidt
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author Maximilian Schmidt <maximilianschmidt404@gmail.com>
 * @copyright (c) 2018, Maximilian Schmidt
 * @license Apache-2.0
 */

namespace Melior\Routing;

use Melior\Core\Exceptions\InvalidConfigurationException;

class Router
{
    /**
     * @Inject("Settings")
     * @var array
     */
    protected $settings;

    /**
     * @Inject
     * @var AltoRouter
     */
    protected $router;

    public function __construct()
    {
        $routes = $this->settings->get('Melior.Routes');

        foreach ($routes as $name => $route) {
            if (!array_key_exists('method', $route)) {
                throw new InvalidConfigurationException("Route configuration '$name' is missing required key 'method'!", 1528065438);
            }

            if (!array_key_exists('path', $route)) {
                throw new InvalidConfigurationException("Route configuration '$name' is missing required key 'path'!", 1528065470);
            }

            if (!array_key_exists('target', $route)) {
                throw new InvalidConfigurationException("Route configuration '$name' is missing require key 'target'!", 1528065516);
            }

            $this->router->map($route['method'], $route['path'], $route['target'], $name);
        }
    }

    public function resolve(string $requestPath)
    {
        $result = $this->router->match($requestPath);

        return $result;
    }
}
