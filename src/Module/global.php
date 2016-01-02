<?php

function app()
{
    return \Fine\Application\Application::getInstance();
}

function h($s)
{
    return htmlspecialchars($s);
}
