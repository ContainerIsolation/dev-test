<?php
namespace Totallywicked\DevTest\Action;

/**
 * Used when no action was matched.
 */
class NotFoundAction extends PageAction
{
    /**
     * Store the title of this page
     * @var string
     */
    protected $title = 'Not found';

    /**
     * Store the layout filename
     * @var string
     */
    protected $layoutFile = 'layout/standard';

    /**
     * Store the template filename
     * @var string
     */
    protected $templateFile = '404';

    /**
     * Response code for this action
     * @var int
     */
    protected $code = 404;
}