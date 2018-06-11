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

use Symfony\Component\HttpFoundation\Request;

use Melior\Core\Exceptions\InvalidConfigurationException;
use Melior\Core\Settings\Settings;

use Melior\Routing\Interfaces\RouterInterface;

class Router extends \AltoRouter implements RouterInterface
{
    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Gathers all route configurations and constructs a new instance.
     *
     * @Inject
     * @param Settings $settings
     * @param Request $request
     */
    public function __construct(Settings $settings, Request $request)
    {
        $this->settings = $settings;
        $this->request = $request;

        $routes = $settings->get('Melior.Routing.routes');

        foreach ($routes as $name => $route) {
            if (!array_key_exists('method', $route)) {
                throw new InvalidConfigurationException(
                    "Route configuration '$name' is missing required key 'method'!",
                    1528065438
                );
            }

            if (!array_key_exists('path', $route)) {
                throw new InvalidConfigurationException(
                    "Route configuration '$name' is missing required key 'path'!",
                    1528065470
                );
            }

            if (!array_key_exists('target', $route)) {
                throw new InvalidConfigurationException(
                    "Route configuration '$name' is missing required key 'target'!",
                    1528065516
                );
            }

            $this->map($route['method'], $route['path'], $route['target'], $name);
        }
    }

    /**
     * Resolves the current route and returns it's desired target.
     *
     * @return string
     */
    public function resolve() : string
    {
        $path = $this->request->query->get('path');
        $method = $this->request->getMethod();

        $result = $this->match($path, $method);

        if (empty($result)) {
            $fallback = $this->settings->get('Melior.Routing.fallbackRoute');
            $result = $this->settings->get("Melior.Routing.routes.$fallback.target");
        }

        return $result;
    }
}
