<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Service;

use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Entity\Product;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 京东CRM管理菜单服务
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('京东CRM')) {
            $item->addChild('京东CRM')
                ->setAttribute('icon', 'fas fa-store')
            ;
        }
        $subMenu = $item->getChild('京东CRM');
        if (null !== $subMenu) {
            // 客户管理
            $subMenu->addChild('客户管理')
                ->setUri($this->linkGenerator->getCurdListPage(Customer::class))
                ->setAttribute('icon', 'fas fa-users')
            ;

            // 联系人管理
            $subMenu->addChild('联系人管理')
                ->setUri($this->linkGenerator->getCurdListPage(Contact::class))
                ->setAttribute('icon', 'fas fa-address-book')
            ;

            // 销售线索
            $subMenu->addChild('销售线索')
                ->setUri($this->linkGenerator->getCurdListPage(Lead::class))
                ->setAttribute('icon', 'fas fa-search-plus')
            ;

            // 销售机会
            $subMenu->addChild('销售机会')
                ->setUri($this->linkGenerator->getCurdListPage(Opportunity::class))
                ->setAttribute('icon', 'fas fa-bullseye')
            ;

            // 订单管理
            $subMenu->addChild('订单管理')
                ->setUri($this->linkGenerator->getCurdListPage(Order::class))
                ->setAttribute('icon', 'fas fa-shopping-cart')
            ;

            // 产品管理
            $subMenu->addChild('产品管理')
                ->setUri($this->linkGenerator->getCurdListPage(Product::class))
                ->setAttribute('icon', 'fas fa-box')
            ;
        }
    }
}
