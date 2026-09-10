<?php

namespace App\Exceptions;

/**
 * A field-operations action that cannot go ahead for a reason the person at
 * the screen can fix ("Only 4 of 20W-50 4L at the warehouse"). Controllers
 * answer it with a 422 and the message as-is.
 */
class OpsException extends \RuntimeException
{
}
