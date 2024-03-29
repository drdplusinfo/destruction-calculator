<?php declare(strict_types=1);

namespace DrdPlus\RulesSkeleton;

use Granam\Scalar\Tools\ToString;
use Granam\Strict\Object\StrictObject;

class RouteMatch extends StrictObject
{
    private ?string $resource = null;
    private ?string $type = null;
    private ?string $prefix = null;
    private ?string $path = null;
    private array $query;
    private ?string $routeName = null;
    private ?string $host = null;
    private $schemes;
    private $methods;
    private $defaults;
    private $requirements;
    private $options;
    private $condition;
    private $controller;
    private $name_prefix;
    private $trailingSlashOnRoot;
    private $locale;
    private $format;
    private $utf8;
    private $exclude;
    private $stateless;

    /**
     * @param array $values
     * @throws \DrdPlus\RulesSkeleton\Exceptions\MissingRequiredPathInRouteMatch
     */
    public function __construct(array $values)
    {
        try {
            $this->path = ToString::toString($values['path'] ?? null /* intentionally NULL to raise an exception in case of missing path */);
        } catch (\Granam\Scalar\Tools\Exceptions\Exception $exception) {
            throw new Exceptions\MissingRequiredPathInRouteMatch('Got matches ' . var_export($values, true));
        }
        $this->routeName = $values['_route'] ?? null;

        $this->query = $this->parseQuery($values['query'] ?? '');
        $this->resource = $values['resource'] ?? null;
        $this->type = $values['type'] ?? null;
        $this->prefix = $values['prefix'] ?? null;
        $this->host = $values['host'] ?? null;
        $this->schemes = $values['schemes'] ?? null;
        $this->methods = $values['methods'] ?? null;
        $this->defaults = $values['defaults'] ?? null;
        $this->requirements = $values['requirements'] ?? null;
        $this->options = $values['options'] ?? null;
        $this->condition = $values['condition'] ?? null;
        $this->controller = $values['controller'] ?? null;
        $this->name_prefix = $values['name_prefix'] ?? null;
        $this->trailingSlashOnRoot = $values['trailing_slash_on_root'] ?? null;
        $this->locale = $values['locale'] ?? null;
        $this->format = $values['format'] ?? null;
        $this->utf8 = $values['utf8'] ?? null;
        $this->exclude = $values['exclude'] ?? null;
        $this->stateless = $values['stateless'] ?? null;
    }

    protected function parseQuery(?string $queryString): array
    {
        parse_str($queryString, $queryArray);
        return $queryArray;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @return string|null
     */
    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return mixed|null
     */
    public function getSchemes()
    {
        return $this->schemes;
    }

    /**
     * @return mixed|null
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return mixed|null
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return mixed
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @return mixed|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function getNamePrefix()
    {
        return $this->name_prefix;
    }

    /**
     * @return mixed
     */
    public function getTrailingSlashOnRoot()
    {
        return $this->trailingSlashOnRoot;
    }

    /**
     * @return mixed|null
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed|null
     */
    public function getUtf8()
    {
        return $this->utf8;
    }

    /**
     * @return mixed|null
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @return mixed|null
     */
    public function getStateless()
    {
        return $this->stateless;
    }

}
