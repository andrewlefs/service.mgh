<?php


namespace MigEvents\Logger\CurlLogger;

use MigEvents\Http\Parameters;

class JsonAwareParameters extends Parameters {

  /**
   * @param mixed $value
   * @return string
   */
  protected function exportNonScalar($value) {
    return JsonNode::factory($value)->encode();
  }
}
