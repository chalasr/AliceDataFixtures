<?php

/*
 * This file is part of the Fidry\AliceDataFixtures package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fidry\AliceDataFixtures\Loader;

use Fidry\AliceDataFixtures\LoaderInterface;
use Nelmio\Alice\FileLoaderInterface;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\ObjectSet;
use Nelmio\Alice\ParameterBag;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @covers Fidry\AliceDataFixtures\Loader\SimpleFileLoader
 */
class SimpleLoaderTest extends TestCase
{
    public function testIsALoader()
    {
        $this->assertTrue(is_a(SimpleFileLoader::class, LoaderInterface::class, true));
    }

    public function testDecoratesAliceLoaderToLoadEachFileGivenAndReturnsTheObjectsLoaded()
    {
        $files = [
            'fixtures1.yml',
        ];

        $fileLoaderProphecy = $this->prophesize(FileLoaderInterface::class);
        $fileLoaderProphecy
            ->loadFile($files[0], [], [])
            ->willReturn(
                new ObjectSet(
                    new ParameterBag(),
                    new ObjectBag([
                        'dummy' => new \stdClass(),
                    ])
                )
            )
        ;
        /** @var FileLoaderInterface $fileLoader */
        $fileLoader = $fileLoaderProphecy->reveal();

        $loader = new SimpleFileLoader($fileLoader);
        $result = $loader->load($files);

        $this->assertEquals(
            [
                'dummy' => new \stdClass(),
            ],
            $result
        );

        $fileLoaderProphecy->loadFile(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function testParametersAndObjectsLoadedAreReinjectedBetweenEachLoader()
    {
        $files = [
            'fixtures1.yml',
            'fixtures2.yml',
        ];

        $fileLoaderProphecy = $this->prophesize(FileLoaderInterface::class);
        $fileLoaderProphecy
            ->loadFile($files[0], [], [])
            ->willReturn(
                new ObjectSet(
                    new ParameterBag([
                        'first' => true,
                    ]),
                    new ObjectBag([
                        'first' => new \stdClass(),
                    ])
                )
            )
        ;
        $fileLoaderProphecy
            ->loadFile(
                $files[1],
                [
                    'first' => true,
                ],
                [
                    'first' => new \stdClass(),
                ]
            )
            ->willReturn(
                new ObjectSet(
                    new ParameterBag([
                        'first' => true,
                        'second' => true,
                    ]),
                    new ObjectBag([
                        'first' => new \stdClass(),
                        'second' => new \stdClass(),
                    ])
                )
            )
        ;
        $fileLoader = $fileLoaderProphecy->reveal();

        $loader = new SimpleFileLoader($fileLoader);
        $result = $loader->load($files);

        $this->assertEquals(
            [
                'first' => new \stdClass(),
                'second' => new \stdClass(),
            ],
            $result
        );

        $fileLoaderProphecy->loadFile(Argument::cetera())->shouldHaveBeenCalledTimes(2);
    }

    public function testParametersAndObjectsPassedAndLoadedAreReinjectedBetweenEachLoader()
    {
        $files = [
            'fixtures1.yml',
            'fixtures2.yml',
        ];
        $parameters = [
            'injected' => true,
        ];
        $objects = [
            'injected' => new \stdClass(),
        ];

        $fileLoaderProphecy = $this->prophesize(FileLoaderInterface::class);
        $fileLoaderProphecy
            ->loadFile($files[0], $parameters, $objects)
            ->willReturn(
                new ObjectSet(
                    new ParameterBag([
                        'injected' => true,
                        'first' => true,
                    ]),
                    new ObjectBag([
                        'injected' => new \stdClass(),
                        'first' => new \stdClass(),
                    ])
                )
            )
        ;
        $fileLoaderProphecy
            ->loadFile(
                $files[1],
                [
                    'injected' => true,
                    'first' => true,
                ],
                [
                    'injected' => new \stdClass(),
                    'first' => new \stdClass(),
                ]
            )
            ->willReturn(
                new ObjectSet(
                    new ParameterBag([
                        'injected' => true,
                        'first' => true,
                        'second' => true,
                    ]),
                    new ObjectBag([
                        'injected' => new \stdClass(),
                        'first' => new \stdClass(),
                        'second' => new \stdClass(),
                    ])
                )
            )
        ;
        $fileLoader = $fileLoaderProphecy->reveal();

        $loader = new SimpleFileLoader($fileLoader);
        $result = $loader->load($files, $parameters, $objects);

        $this->assertEquals(
            [
                'injected' => new \stdClass(),
                'first' => new \stdClass(),
                'second' => new \stdClass(),
            ],
            $result
        );

        $fileLoaderProphecy->loadFile(Argument::cetera())->shouldHaveBeenCalledTimes(2);
    }
}
