<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 客户管理控制器
 *
 * @extends AbstractCrudController<Customer>
 */
#[AdminCrud(routePath: '/jingdong-crm/customer', routeName: 'jingdong_crm_customer')]
#[Autoconfigure(public: true)]
final class CustomerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('客户')
            ->setEntityLabelInPlural('客户管理')
            ->setPageTitle('index', '客户列表')
            ->setPageTitle('detail', '客户详情')
            ->setPageTitle('new', '新建客户')
            ->setPageTitle('edit', '编辑客户')
            ->setHelp('index', '管理京东CRM系统中的客户信息')
            ->setDefaultSort(['updateTime' => 'DESC'])
            ->setSearchFields(['name', 'customerCode', 'jdCustomerId', 'email', 'phone'])
            ->setPaginatorPageSize(20)
        ;
    }

    /**
     * @return iterable<FieldInterface|string>
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('customerCode', '客户编码')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('客户的唯一编码标识，用于系统内部识别')
        ;

        yield TextField::new('name', '客户名称')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('客户的完整名称')
        ;

        $typeField = EnumField::new('type', '客户类型');
        $typeField->setEnumCases(CustomerTypeEnum::cases());
        $typeField->setRequired(true);
        yield $typeField
            ->setHelp('客户的类型分类')
            ->renderAsBadges([
                CustomerTypeEnum::INDIVIDUAL->value => 'primary',
                CustomerTypeEnum::ENTERPRISE->value => 'success',
            ])
        ;

        yield EmailField::new('email', '邮箱')
            ->setRequired(false)
            ->setHelp('客户的联系邮箱地址')
            ->hideOnIndex()
        ;

        yield TelephoneField::new('phone', '电话')
            ->setRequired(false)
            ->setHelp('客户的联系电话号码')
        ;

        yield TextareaField::new('address', '地址')
            ->setRequired(false)
            ->setMaxLength(1000)
            ->setHelp('客户的详细地址信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 3],
            ])
        ;

        $statusField = EnumField::new('status', '状态');
        $statusField->setEnumCases(CustomerStatusEnum::cases());
        $statusField->setRequired(true);
        yield $statusField
            ->setHelp('客户的当前状态')
            ->renderAsBadges([
                CustomerStatusEnum::ACTIVE->value => 'success',
                CustomerStatusEnum::SUSPENDED->value => 'warning',
                CustomerStatusEnum::CLOSED->value => 'danger',
            ])
        ;

        yield TextField::new('jdCustomerId', '京东客户ID')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('京东系统中的客户唯一标识符')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '客户名称'))
            ->add(TextFilter::new('customerCode', '客户编码'))
            ->add(TextFilter::new('jdCustomerId', '京东客户ID'))
            ->add(ChoiceFilter::new('type', '客户类型')
                ->setChoices($this->getCustomerTypeChoices()))
            ->add(ChoiceFilter::new('status', '状态')
                ->setChoices($this->getCustomerStatusChoices()))
            ->add(TextFilter::new('email', '邮箱'))
            ->add(TextFilter::new('phone', '电话'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function createEntity(string $entityFqcn): Customer
    {
        $customer = new Customer();

        // 设置默认值
        $customer->setType(CustomerTypeEnum::INDIVIDUAL);
        $customer->setStatus(CustomerStatusEnum::ACTIVE);

        return $customer;
    }

    /**
     * 获取客户类型选项
     *
     * @return array<string, string>
     */
    private function getCustomerTypeChoices(): array
    {
        $choices = [];
        foreach (CustomerTypeEnum::cases() as $type) {
            $choices[$type->getLabel()] = $type->value;
        }

        return $choices;
    }

    /**
     * 获取客户状态选项
     *
     * @return array<string, string>
     */
    private function getCustomerStatusChoices(): array
    {
        $choices = [];
        foreach (CustomerStatusEnum::cases() as $status) {
            $choices[$status->getLabel()] = $status->value;
        }

        return $choices;
    }
}
