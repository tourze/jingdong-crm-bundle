<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;

/**
 * 产品测试数据
 */
class ProductFixtures extends Fixture
{
    public const PRODUCT_LAPTOP = 'jingdong-crm-product-laptop';
    public const PRODUCT_PHONE = 'jingdong-crm-product-phone';
    public const PRODUCT_TABLET = 'jingdong-crm-product-tablet';
    public const PRODUCT_HEADPHONES = 'jingdong-crm-product-headphones';
    public const PRODUCT_CAMERA = 'jingdong-crm-product-camera';

    public function load(ObjectManager $manager): void
    {
        // 笔记本电脑
        $laptop = new Product();
        $laptop->setProductCode('JD-LAPTOP-001');
        $laptop->setName('ThinkPad X1 Carbon 商务笔记本');
        $laptop->setCategory('电脑数码');
        $laptop->setDescription('轻薄商务笔记本，14英寸高清屏幕，第11代Intel酷睿处理器，16GB内存，512GB固态硬盘');
        $laptop->setPrice('9999.00');
        $laptop->setUnit('台');
        $laptop->setStatus(ProductStatus::ON_SALE);
        $laptop->setJdProductId('JD100012345678');

        $manager->persist($laptop);
        $this->addReference(self::PRODUCT_LAPTOP, $laptop);

        // 智能手机
        $phone = new Product();
        $phone->setProductCode('JD-PHONE-001');
        $phone->setName('iPhone 14 Pro 智能手机');
        $phone->setCategory('手机通讯');
        $phone->setDescription('6.1英寸Super Retina XDR显示屏，A16仿生芯片，专业级摄像头系统');
        $phone->setPrice('7999.00');
        $phone->setUnit('部');
        $phone->setStatus(ProductStatus::ON_SALE);
        $phone->setJdProductId('JD100087654321');

        $manager->persist($phone);
        $this->addReference(self::PRODUCT_PHONE, $phone);

        // 平板电脑
        $tablet = new Product();
        $tablet->setProductCode('JD-TABLET-001');
        $tablet->setName('iPad Air 平板电脑');
        $tablet->setCategory('电脑数码');
        $tablet->setDescription('10.9英寸Liquid视网膜显示屏，M1芯片，全天候电池续航');
        $tablet->setPrice('4399.00');
        $tablet->setUnit('台');
        $tablet->setStatus(ProductStatus::ON_SALE);
        $tablet->setJdProductId('JD100011223344');

        $manager->persist($tablet);
        $this->addReference(self::PRODUCT_TABLET, $tablet);

        // 无线耳机
        $headphones = new Product();
        $headphones->setProductCode('JD-HEADPHONES-001');
        $headphones->setName('AirPods Pro 无线降噪耳机');
        $headphones->setCategory('数码配件');
        $headphones->setDescription('主动降噪，通透模式，空间音频，最长6小时聞歌时间');
        $headphones->setPrice('1999.00');
        $headphones->setUnit('副');
        $headphones->setStatus(ProductStatus::OUT_OF_STOCK);
        $headphones->setJdProductId('JD100055667788');

        $manager->persist($headphones);
        $this->addReference(self::PRODUCT_HEADPHONES, $headphones);

        // 数码相机
        $camera = new Product();
        $camera->setProductCode('JD-CAMERA-001');
        $camera->setName('Sony A7 III 全画幅微单相机');
        $camera->setCategory('摄影摄像');
        $camera->setDescription('2420万有效像素，5轴防抖，4K视频录制，693个对焦点');
        $camera->setPrice('12999.00');
        $camera->setUnit('台');
        $camera->setStatus(ProductStatus::OFF_SHELF);
        $camera->setJdProductId('JD100099887766');

        $manager->persist($camera);
        $this->addReference(self::PRODUCT_CAMERA, $camera);

        $manager->flush();
    }
}
