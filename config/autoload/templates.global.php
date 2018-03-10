<?php
/**
 * This file is part of prooph/proophessor-do.
 * (c) 2014-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2015-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\ProophessorDo;

use Zend\Expressive;
use Zend\View;

return [
    'dependencies' => [
        'factories' => [
            Expressive\Template\TemplateRendererInterface::class => Expressive\ZendView\ZendViewRendererFactory::class,
            View\HelperPluginManager::class => Expressive\ZendView\HelperPluginManagerFactory::class,
            //Custom view plugins
            \Prooph\ProophessorDo\App\View\Helper\RiotTag::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
    'templates' => [
        'layout' => 'app::layout',
        'map' => [
            'error/error' => 'templates/error/error.phtml',
            'error/404' => 'templates/error/404.phtml',
            //html templates
            'app::layout' => 'templates/layout/layout.phtml',
            'page::home' => 'templates/action/home.phtml',
            'page::user-list' => 'templates/action/user-list.phtml',
            'page::user-registration' => 'templates/action/user-registration-form.phtml',
            'page::user-todo-list' => 'templates/action/user-todo-list.phtml',
            'page::user-todo-form' => 'templates/action/user-todo-form.phtml',
            //riot tags
            'riot::user-form' => 'templates/riot/user-form.phtml',
            'riot::user-todo-form' => 'templates/riot/user-todo-form.phtml',
            'riot::user-todo-list' => 'templates/riot/user-todo-list.phtml',
            'riot::user-todo' => 'templates/riot/user-todo.phtml',
        ],
        'paths' => [
            'app' => ['templates/app'],
            'layout' => ['templates/layout'],
            'error' => ['templates/error'],
        ],
    ],
    'view_helpers' => [
        // zend-servicemanager-style configuration for adding view helpers:
        // - 'aliases'
        // - 'invokables'
        // - 'factories'
        // - 'abstract_factories'
        // - etc.
        'invokables' => [
            'riotTag' => \Prooph\ProophessorDo\App\View\Helper\RiotTag::class,
        ],
    ],
];
