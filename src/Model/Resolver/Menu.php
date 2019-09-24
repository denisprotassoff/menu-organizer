<?php
/**
 * ScandiPWA_MenuOrganizerGraphQl
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_MenuOrganizer
 * @author      Raivis Dejus <info@scandiweb.com>
 * @copyright   Copyright (c) 2018 Scandiweb, Ltd (https://scandiweb.com)
 */
declare(strict_types=1);

namespace ScandiPWA\MenuOrganizer\Model\Resolver;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ScandiPWA\MenuOrganizer\Model\MenuFactory;
use ScandiPWA\MenuOrganizer\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;

/**
 * Class Menu
 *
 * @package ScandiPWA\MenumanagerGraphQl\Model\Resolver
 */
class Menu implements ResolverInterface
{
    const CATEGORY_ID_KEY = 'category_id';

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Menu constructor.
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        MenuFactory $menuFactory,
        ItemCollectionFactory $itemCollectionFactory,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->menuFactory = $menuFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Menu organizer resolver (lines concerning menu id are changed from core scandipwa menumanager)
     *
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $identifier = $args['identifier'];

        $menu = $this->menuFactory
            ->create()
            ->load($identifier);

        if ($menu->getId() === null) {
            throw new \InvalidArgumentException("Could not find menu with identifier '${identifier}'");
        }

        return array_merge(
            $menu->getData(),
            [
                'items' => $this->getMenuItems($menu['menu_id'])
            ]);
    }

    /**
     * @param string $menuId
     * @return array
     */
    private function getMenuItems(string $menuId): array
    {
        $menuItems = $this->itemCollectionFactory
            ->create()
            ->addMenuFilter($menuId)
            ->addStatusFilter()
            ->setParentIdOrder()
            ->setPositionOrder()
            ->getData();

        return array_map(function ($item) {
            if (isset($item[static::CATEGORY_ID_KEY])) {
                $categoryUrlPath = $this->categoryRepository
                    ->get($item[static::CATEGORY_ID_KEY])
                    ->getUrlPath();

                $item['url'] = "/${categoryUrlPath}";
            }

            return $item;
        }, $menuItems);
    }
}
