<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Enum\OrderStatus;

/**
 * 订单测试数据
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const ORDER_JANE_SMITH_PHONE = 'jingdong-crm-order-jane-smith-phone';
    public const ORDER_JOHN_DOE_LAPTOP = 'jingdong-crm-order-john-doe-laptop';
    public const ORDER_TECH_CORP_EQUIPMENT = 'jingdong-crm-order-tech-corp-equipment';
    public const ORDER_DIGITAL_SOLUTIONS_SOFTWARE = 'jingdong-crm-order-digital-solutions-software';
    public const ORDER_INDIVIDUAL_ACCESSORIES = 'jingdong-crm-order-individual-accessories';

    public function load(ObjectManager $manager): void
    {
        // 李小红的iPhone订单 - 已完成
        $janeSmithPhoneOrder = new Order();
        $janeSmithPhoneOrder->setOrderNumber('ORD-2024-001');
        $janeSmithPhoneOrder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_JANE_SMITH, Customer::class));
        $janeSmithPhoneOrder->setOpportunity($this->getReference(OpportunityFixtures::OPPORTUNITY_JANE_SMITH_PHONE, Opportunity::class));
        $janeSmithPhoneOrder->setStatus(OrderStatus::COMPLETED);
        $janeSmithPhoneOrder->setTotalAmount('15998.00');
        $janeSmithPhoneOrder->setPaidAmount('15998.00');
        $janeSmithPhoneOrder->setDiscountAmount('0.00');
        $janeSmithPhoneOrder->setOrderDate(new \DateTimeImmutable('-1 week'));
        $janeSmithPhoneOrder->setPaymentMethod('支付宝');
        $janeSmithPhoneOrder->setShippingAddress('四川省成都市高新区天府大道软件园A座1205室');
        $janeSmithPhoneOrder->setJdOrderId('JD202401001');
        $janeSmithPhoneOrder->setNotes('客户要求原装正品，已按时发货并送达');

        $manager->persist($janeSmithPhoneOrder);
        $this->addReference(self::ORDER_JANE_SMITH_PHONE, $janeSmithPhoneOrder);

        // 张明的笔记本订单 - 配送中
        $johnDoeLaptopOrder = new Order();
        $johnDoeLaptopOrder->setOrderNumber('ORD-2024-002');
        $johnDoeLaptopOrder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_JOHN_DOE, Customer::class));
        $johnDoeLaptopOrder->setOpportunity($this->getReference(OpportunityFixtures::OPPORTUNITY_JOHN_DOE_LAPTOP, Opportunity::class));
        $johnDoeLaptopOrder->setStatus(OrderStatus::SHIPPING);
        $johnDoeLaptopOrder->setTotalAmount('49995.00');
        $johnDoeLaptopOrder->setPaidAmount('49995.00');
        $johnDoeLaptopOrder->setDiscountAmount('500.00');
        $johnDoeLaptopOrder->setOrderDate(new \DateTimeImmutable('-3 days'));
        $johnDoeLaptopOrder->setPaymentMethod('银行转账');
        $johnDoeLaptopOrder->setShippingAddress('广东省深圳市南山区科技园南区B栋802室');
        $johnDoeLaptopOrder->setJdOrderId('JD202401002');
        $johnDoeLaptopOrder->setNotes('批量采购，享受企业客户折扣，正在配送中');

        $manager->persist($johnDoeLaptopOrder);
        $this->addReference(self::ORDER_JOHN_DOE_LAPTOP, $johnDoeLaptopOrder);

        // 科技公司设备采购订单 - 已支付
        $techCorpEquipmentOrder = new Order();
        $techCorpEquipmentOrder->setOrderNumber('ORD-2024-003');
        $techCorpEquipmentOrder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_TECH_CORP, Customer::class));
        $techCorpEquipmentOrder->setOpportunity($this->getReference(OpportunityFixtures::OPPORTUNITY_TECH_CORP_SYSTEM, Opportunity::class));
        $techCorpEquipmentOrder->setStatus(OrderStatus::PAID);
        $techCorpEquipmentOrder->setTotalAmount('125000.00');
        $techCorpEquipmentOrder->setPaidAmount('125000.00');
        $techCorpEquipmentOrder->setDiscountAmount('5000.00');
        $techCorpEquipmentOrder->setOrderDate(new \DateTimeImmutable('-1 day'));
        $techCorpEquipmentOrder->setPaymentMethod('对公转账');
        $techCorpEquipmentOrder->setShippingAddress('北京市海淀区中关村大街123号科技大厦15层');
        $techCorpEquipmentOrder->setJdOrderId('JD202401003');
        $techCorpEquipmentOrder->setNotes('企业级采购，包含服务器、网络设备等，等待安排发货');

        $manager->persist($techCorpEquipmentOrder);
        $this->addReference(self::ORDER_TECH_CORP_EQUIPMENT, $techCorpEquipmentOrder);

        // 数字化解决方案公司软件订单 - 待支付
        $digitalSolutionsSoftwareOrder = new Order();
        $digitalSolutionsSoftwareOrder->setOrderNumber('ORD-2024-004');
        $digitalSolutionsSoftwareOrder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_DIGITAL_SOLUTIONS, Customer::class));
        $digitalSolutionsSoftwareOrder->setOpportunity($this->getReference(OpportunityFixtures::OPPORTUNITY_DIGITAL_SOLUTIONS_PLATFORM, Opportunity::class));
        $digitalSolutionsSoftwareOrder->setStatus(OrderStatus::PENDING_PAYMENT);
        $digitalSolutionsSoftwareOrder->setTotalAmount('580000.00');
        $digitalSolutionsSoftwareOrder->setPaidAmount('0.00');
        $digitalSolutionsSoftwareOrder->setDiscountAmount('20000.00');
        $digitalSolutionsSoftwareOrder->setOrderDate(new \DateTimeImmutable('today'));
        $digitalSolutionsSoftwareOrder->setPaymentMethod('银行承兑汇票');
        $digitalSolutionsSoftwareOrder->setShippingAddress('上海市浦东新区陆家嘴金融贸易区世纪大道1000号');
        $digitalSolutionsSoftwareOrder->setJdOrderId('JD202401004');
        $digitalSolutionsSoftwareOrder->setNotes('大型软件许可采购，客户正在走审批流程');

        $manager->persist($digitalSolutionsSoftwareOrder);
        $this->addReference(self::ORDER_DIGITAL_SOLUTIONS_SOFTWARE, $digitalSolutionsSoftwareOrder);

        // 个人客户配件订单 - 已取消
        $individualAccessoriesOrder = new Order();
        $individualAccessoriesOrder->setOrderNumber('ORD-2024-005');
        $individualAccessoriesOrder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_JOHN_DOE, Customer::class));
        $individualAccessoriesOrder->setStatus(OrderStatus::CANCELLED);
        $individualAccessoriesOrder->setTotalAmount('1999.00');
        $individualAccessoriesOrder->setPaidAmount('0.00');
        $individualAccessoriesOrder->setDiscountAmount('0.00');
        $individualAccessoriesOrder->setOrderDate(new \DateTimeImmutable('-5 days'));
        $individualAccessoriesOrder->setPaymentMethod('微信支付');
        $individualAccessoriesOrder->setShippingAddress('广东省深圳市南山区科技园南区B栋802室');
        $individualAccessoriesOrder->setJdOrderId('JD202401005');
        $individualAccessoriesOrder->setNotes('客户取消订单，原因：找到更优惠的价格');

        $manager->persist($individualAccessoriesOrder);
        $this->addReference(self::ORDER_INDIVIDUAL_ACCESSORIES, $individualAccessoriesOrder);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
            OpportunityFixtures::class,
        ];
    }
}
