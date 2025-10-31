<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;

/**
 * 客户测试数据
 */
class CustomerFixtures extends Fixture
{
    public const CUSTOMER_TECH_CORP = 'jingdong-crm-customer-tech-corp';
    public const CUSTOMER_DIGITAL_SOLUTIONS = 'jingdong-crm-customer-digital-solutions';
    public const CUSTOMER_JOHN_DOE = 'jingdong-crm-customer-john-doe';
    public const CUSTOMER_STARTUP_INC = 'jingdong-crm-customer-startup-inc';
    public const CUSTOMER_JANE_SMITH = 'jingdong-crm-customer-jane-smith';

    public function load(ObjectManager $manager): void
    {
        // 企业客户 - 科技公司
        $techCorp = new Customer();
        $techCorp->setCustomerCode('CUS-TECH-001');
        $techCorp->setName('北京科技有限公司');
        $techCorp->setType(CustomerTypeEnum::ENTERPRISE);
        $techCorp->setEmail('contact@tech-corp.com');
        $techCorp->setPhone('010-12345678');
        $techCorp->setAddress('北京市海淀区中关村大街123号科技大厦15层');
        $techCorp->setStatus(CustomerStatusEnum::ACTIVE);
        $techCorp->setJdCustomerId('JD-CUS-001');

        $manager->persist($techCorp);
        $this->addReference(self::CUSTOMER_TECH_CORP, $techCorp);

        // 企业客户 - 数字化解决方案公司
        $digitalSolutions = new Customer();
        $digitalSolutions->setCustomerCode('CUS-DIGITAL-001');
        $digitalSolutions->setName('上海数字化解决方案集团');
        $digitalSolutions->setType(CustomerTypeEnum::ENTERPRISE);
        $digitalSolutions->setEmail('business@digital-solutions.com');
        $digitalSolutions->setPhone('021-87654321');
        $digitalSolutions->setAddress('上海市浦东新区陆家嘴金融贸易区世纪大道1000号');
        $digitalSolutions->setStatus(CustomerStatusEnum::ACTIVE);
        $digitalSolutions->setJdCustomerId('JD-CUS-002');

        $manager->persist($digitalSolutions);
        $this->addReference(self::CUSTOMER_DIGITAL_SOLUTIONS, $digitalSolutions);

        // 个人客户 - 张先生
        $johnDoe = new Customer();
        $johnDoe->setCustomerCode('CUS-IND-001');
        $johnDoe->setName('张明');
        $johnDoe->setType(CustomerTypeEnum::INDIVIDUAL);
        $johnDoe->setEmail('zhangming@email.com');
        $johnDoe->setPhone('138-0000-1234');
        $johnDoe->setAddress('广东省深圳市南山区科技园南区');
        $johnDoe->setStatus(CustomerStatusEnum::ACTIVE);
        $johnDoe->setJdCustomerId('JD-CUS-003');

        $manager->persist($johnDoe);
        $this->addReference(self::CUSTOMER_JOHN_DOE, $johnDoe);

        // 企业客户 - 创业公司
        $startupInc = new Customer();
        $startupInc->setCustomerCode('CUS-STARTUP-001');
        $startupInc->setName('杭州创新科技创业公司');
        $startupInc->setType(CustomerTypeEnum::ENTERPRISE);
        $startupInc->setEmail('info@startup-inc.com');
        $startupInc->setPhone('0571-23456789');
        $startupInc->setAddress('浙江省杭州市西湖区文三路创业大厦');
        $startupInc->setStatus(CustomerStatusEnum::SUSPENDED);
        $startupInc->setJdCustomerId('JD-CUS-004');

        $manager->persist($startupInc);
        $this->addReference(self::CUSTOMER_STARTUP_INC, $startupInc);

        // 个人客户 - 李女士
        $janeSmith = new Customer();
        $janeSmith->setCustomerCode('CUS-IND-002');
        $janeSmith->setName('李小红');
        $janeSmith->setType(CustomerTypeEnum::INDIVIDUAL);
        $janeSmith->setEmail('lixiaohong@email.com');
        $janeSmith->setPhone('186-5555-6789');
        $janeSmith->setAddress('四川省成都市高新区天府大道软件园');
        $janeSmith->setStatus(CustomerStatusEnum::ACTIVE);
        $janeSmith->setJdCustomerId('JD-CUS-005');

        $manager->persist($janeSmith);
        $this->addReference(self::CUSTOMER_JANE_SMITH, $janeSmith);

        $manager->flush();
    }
}
