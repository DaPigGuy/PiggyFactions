<?php

/*
 * libasynql
 *
 * Copyright (C) 2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\libs\poggit\libasynql\base;

use DaPigGuy\PiggyFactions\libs\poggit\libasynql\SqlError;
use DaPigGuy\PiggyFactions\libs\poggit\libasynql\SqlResult;
use Threaded;
use function is_string;
use function serialize;
use function unserialize;

class QueryRecvQueue extends Threaded{
	public function publishResult(int $queryId, SqlResult $result) : void{
		$this[] = serialize([$queryId, $result]);
	}

	public function publishError(int $queryId, SqlError $error) : void{
		$this[] = serialize([$queryId, $error]);
	}

	public function fetchResult(&$queryId, &$result) : bool{
		$row = $this->shift();
		if(is_string($row)){
			[$queryId, $result] = unserialize($row, ["allowed_classes" => true]);
			return true;
		}
		return false;
	}
}
