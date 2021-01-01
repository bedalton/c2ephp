<?php

namespace C2ePhp\Agents\COB;

/**
 * Defines a dependency which is used in a COB file
 * @package C2ePhp\Agents\COB
 */
class COBDependency {
    /// @cond INTERNAL_DOCS

    private $type;
    private $name;

    /// @endcond

    /// @brief Creates a new COBDependency
    /**
     * @param string $type The type of dependency ('sprite' or 'sound').
     * @param string $name The name of the dependency (four characters, no file extension)
     */
    public function __construct($type, $name) {
        $this->type = $type;
        $this->name = $name;
    }
    /// @brief Gets the dependency type
    /** @return string
     */
    public function getType() {
        return $this->type;
    }
    /// @brief Gets the name of the dependency
    /** @return string
     */
    public function getName() {
        return $this->name;
    }

}