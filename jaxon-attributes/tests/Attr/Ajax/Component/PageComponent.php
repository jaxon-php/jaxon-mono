<?php
declare(strict_types=1);

namespace Jaxon\Attributes\Tests\Attr\Ajax\Component;

use Jaxon\Attributes\Tests\Attr\PageComponent as BaseComponent;

class PageComponent extends BaseComponent
{
    /**
     * @return int
     */
    protected function count(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    protected function limit(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function html(): string
    {
        return '';
    }
}
