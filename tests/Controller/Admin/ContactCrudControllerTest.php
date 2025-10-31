<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use JingdongCrmBundle\Controller\Admin\ContactCrudController;
use JingdongCrmBundle\Entity\Contact;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * ContactCrudController测试类
 * @internal
 */
#[CoversClass(ContactCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ContactCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Contact>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ContactCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '客户' => ['客户'];
        yield '姓名' => ['姓名'];
        yield '职位' => ['职位'];
        yield '邮箱' => ['邮箱'];
        yield '电话' => ['电话'];
        yield '手机' => ['手机'];
        yield '主要联系人' => ['主要联系人'];
        yield '状态' => ['状态'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'customer' => ['customer'];
        yield 'name' => ['name'];
        yield 'title' => ['title'];
        yield 'email' => ['email'];
        yield 'phone' => ['phone'];
        yield 'mobile' => ['mobile'];
        yield 'isPrimary' => ['isPrimary'];
        yield 'status' => ['status'];
    }

    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    public function testGetEntityFqcn(): void
    {
        $fqcn = ContactCrudController::getEntityFqcn();
        self::assertEquals(Contact::class, $fqcn);
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();
        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertResponseIsSuccessful();

        // 获取表单并设置无效数据来触发验证错误
        $form = $crawler->selectButton('Create')->form();
        $entityName = $this->getEntitySimpleName();

        // 提交空表单（留空必填字段）
        $crawler = $client->submit($form);

        // 验证返回状态码（422 Unprocessable Entity 或重定向到表单页面显示错误）
        if (422 === $client->getResponse()->getStatusCode()) {
            $this->assertResponseStatusCodeSame(422);
            // 验证必填字段错误信息
            $errorMessages = $crawler->filter('.invalid-feedback')->text();
            // Contact必填字段：customer (NotNull) 和 name (NotBlank)
            // 验证必填字段的验证错误信息
            $hasValidationError = false !== strpos($errorMessages, 'should not be blank')
                                 || false !== strpos($errorMessages, 'should not be null')
                                 || false !== strpos($errorMessages, 'This value should not be blank')
                                 || false !== strpos($errorMessages, 'This value should not be null');
            self::assertTrue($hasValidationError, '应该有必填字段的验证错误信息: ' . $errorMessages);
        } else {
            // 如果是重定向，检查错误信息是否在新页面显示
            $errorMessages = $crawler->filter('.alert-danger, .invalid-feedback')->text();
            self::assertNotEmpty($errorMessages, '应该有验证错误信息显示');
        }
    }
}
