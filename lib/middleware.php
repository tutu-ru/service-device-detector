<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

/**
 * Middlewares выполняются начиная с последнего.
 * Последний middleware в этом файле будет выполнен первым.
 */

use \RMS\DeviceDetector\Middleware\RequestTiming;

/**
 * Этот middleware должен оставаться последним в файле, т.к. здесь идет замер времени выполнения запроса,
 * а middlewares выполняются от последнего к первому
 */
$app->add(new RequestTiming());
