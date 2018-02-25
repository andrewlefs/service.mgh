<?php

/**
 * Copyright (c) 2014-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace GraphShare\Object;

abstract class AbstractCrudObject extends AbstractObject {

    /**
     * @var string
     */
    const FIELD_ID = 'id';

    /**
     * @var string[] set of fields to read by default
     */
    protected static $defaultReadFields = array();

    /**
     * @var array set of fields that have been mutated
     */
    protected $changedFields = array();

    /**
     * @var string ID of the adaccount this object belongs to
     */
    protected $parentId;

    /**
     * @param string $id Optional (do not set for new objects)
     * @param string $parent_id Optional, needed for creating new objects.
     * @param Api $api The Api instance this object should use to make calls
     */
    public function __construct($id = null, $parent_id = null) {
        parent::__construct();
        $this->data[static::FIELD_ID] = $id;
        $this->parentId = $parent_id;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->data[static::FIELD_ID] = $id;
    }

    /**
     * @param string $parent_id
     */
    public function setParentId($parent_id) {
        $this->parentId = $parent_id;
    }

    /**
     * @return string
     */
    abstract protected function getEndpoint();

    /**
     * @return string|null
     */
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function assureParentId() {
        if (!$this->parentId) {
            throw new \Exception("A parent ID is required.");
        }

        return $this->parentId;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function assureId() {
        if (!$this->data[static::FIELD_ID]) {
            throw new \Exception("field '" . static::FIELD_ID . "' is required.");
        }

        return (string) $this->data[static::FIELD_ID];
    }

    /**
     * Get the values which have changed
     *
     * @return array Key value pairs of changed variables
     */
    public function getChangedValues() {
        return $this->changedFields;
    }

    /**
     * Get the name of the fields that have changed
     *
     * @return array Array of changed field names
     */
    public function getChangedFields() {
        return array_keys($this->changedFields);
    }

    /**
     * Get the values which have changed, converting them to scalars
     */
    public function exportData() {
        $data = array();
        foreach ($this->changedFields as $key => $val) {
            $data[$key] = $val instanceof AbstractObject ? $val->exportData() : $val;
        }

        return $data;
    }

    /**
     * @return void
     */
    protected function clearHistory() {
        $this->changedFields = array();
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        if (!array_key_exists($name, $this->data) || $this->data[$name] !== $value) {

            $this->changedFields[$name] = $value;
        }
        parent::__set($name, $value);
    }
    

    /**
     * @param string[] $fields
     */
    public static function setDefaultReadFields(array $fields = array()) {
        static::$defaultReadFields = $fields;
    }

    /**
     * @return string[]
     */
    public static function getDefaultReadFields() {
        return static::$defaultReadFields;
    }

    /**
     * @return string
     */
    protected function getNodePath() {
        return '/' . $this->assureId();
    }

    /**
     * Create function for the object.
     *
     * @param array $params Additional parameters to include in the request
     * @return $this
     * @throws \Exception
     */
    public function create(array $params = array()) {
        
    }

    /**
     * Update the object. Function parameters are similar with the create function
     *
     * @param array $params Update parameters in assoc
     * @return $this
     */
    public function update(array $params = array()) {
        
    }

    /**
     * Delete this object from the graph
     *
     * @param array $params
     * @return void
     */
    public function delete(array $params = array()) {
        
    }

    /**
     * Perform object upsert
     *
     * Helper function which determines whether an object should be created or
     * updated
     *
     * @param array $params
     * @return $this
     */
    public function save(array $params = array()) {
        
    }

    /**
     * Delete objects.
     *
     * Used batch API calls to delete multiple objects at once
     *
     * @param string[] $ids Array or single Object ID to delete
     * @param Api $api Api Object to use
     * @return bool Returns true on success
     */
    public static function deleteIds(array $ids, api $api = null) {
        
    }

    /**
     * Read function for the object. Convert fields and filters into the query
     * part of uri and return objects.
     *
     * @param mixed $ids Array or single object IDs
     * @param array $fields Array of field names to read
     * @param array $params Additional filters for the reading, in assoc
     * @param Api $api Api Object to use
     * @return Cursor
     */
    public static function readIds(
    array $ids, array $fields = array(), array $params = array()) {
        
    }

}
