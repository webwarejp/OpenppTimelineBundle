<?php

namespace Openpp\TimelineBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenppTimelineExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('timeline.xml');

        if ('orm' == $config['drivers']) {
            $this->registerDoctrineMapping($config);
        }
    }

    /**
     * Registers doctrine mapping on concrete push notification entities
     *
     * @param array $config
     *
     * @return void
     */
    protected function registerDoctrineMapping(array $config)
    {
        $collector = DoctrineCollector::getInstance();

        // One-To-Many Bidirectional for Action and ActionComponent
        $collector->addAssociation($config['class']['action'], 'mapOneToMany', array(
            'fieldName'     => 'actionComponents',
            'targetEntity'  => $config['class']['action_component'],
            'cascade'       => array(
                'persist',
            ),
            'mappedBy'      => 'action',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['action_component'], 'mapManyToOne', array(
            'fieldName'     => 'action',
            'targetEntity'  => $config['class']['action'],
            'inversedBy'    => 'actionComponents',
            'joinColumns'   =>  array(
                array(
                    'name'  => 'action_id',
                    'referencedColumnName' => 'id',
                    'onDelete'      => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // One-To-Many Bidirectional for Action and Timeline
        $collector->addAssociation($config['class']['action'], 'mapOneToMany', array(
            'fieldName'     => 'timelines',
            'targetEntity'  => $config['class']['timeline'],
            'mappedBy'      => 'action',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['timeline'], 'mapManyToOne', array(
            'fieldName'     => 'action',
            'targetEntity'  => $config['class']['action'],
            'inversedBy'    => 'timelines',
            'joinColumns'   =>  array(
                array(
                    'name'  => 'action_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // Many-To-One Unidirectional for ActionComponent and Component
        $collector->addAssociation($config['class']['action_component'], 'mapManyToOne', array(
            'fieldName' => 'component',
            'targetEntity' => $config['class']['component'],
            'joinColumns'   =>  array(
                array(
                    'name'  => 'component_id',
                    'referencedColumnName' => 'id',
                    'onDelete'      => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // Many-To-One Unidirectional for Timeline and Component
        $collector->addAssociation($config['class']['timeline'], 'mapManyToOne', array(
            'fieldName' => 'subject',
            'targetEntity' => $config['class']['component'],
            'joinColumns'   =>  array(
                array(
                    'name'  => 'subject_id',
                    'referencedColumnName' => 'id',
                    'onDelete'      => 'CASCADE',
                ),
            ),
            'orphanRemoval' => false,
        ));
    }
}

