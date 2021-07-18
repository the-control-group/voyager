<?php

namespace TCG\Voyager\Commands;

use Arrilot\Widgets\Console\WidgetMakeCommand;

class MakeWidgetCommand extends WidgetMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:make:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Widget';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/widget.stub';
    }
}
