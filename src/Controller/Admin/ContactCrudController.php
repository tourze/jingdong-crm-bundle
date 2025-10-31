<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\ContactStatusEnum;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * 联系人管理控制器
 * @extends AbstractCrudController<Contact>
 */
#[AdminCrud(routePath: '/jingdong-crm/contact', routeName: 'jingdong_crm_contact')]
final class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('联系人')
            ->setEntityLabelInPlural('联系人管理')
            ->setPageTitle('index', '联系人列表')
            ->setPageTitle('new', '新增联系人')
            ->setPageTitle('edit', '编辑联系人')
            ->setPageTitle('detail', '联系人详情')
            ->setHelp('index', '管理京东CRM系统的客户联系人信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setSearchFields(['name', 'title', 'email', 'phone', 'mobile', 'customer.name', 'customer.customerCode'])
            ->addFormTheme('@EasyAdmin/crud/form_theme.html.twig')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        // 状态选项
        $statusChoices = [];
        foreach (ContactStatusEnum::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('customer', '客户')->setFormTypeOptions([
                'class' => Customer::class,
                'choice_label' => function (Customer $customer): string {
                    return sprintf('%s (%s)', $customer->getName(), $customer->getCustomerCode());
                },
            ]))
            ->add(TextFilter::new('name', '姓名'))
            ->add(TextFilter::new('title', '职位'))
            ->add(TextFilter::new('email', '邮箱'))
            ->add(TextFilter::new('phone', '电话'))
            ->add(TextFilter::new('mobile', '手机'))
            ->add(BooleanFilter::new('isPrimary', '主要联系人'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices($statusChoices))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本信息标签页
        yield FormField::addTab('基本信息');

        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield AssociationField::new('customer', '客户')
            ->setRequired(true)
            ->setFormTypeOptions([
                'choice_label' => function (Customer $customer): string {
                    return sprintf('%s (%s)', $customer->getName(), $customer->getCustomerCode());
                },
                'placeholder' => '请选择客户',
            ])
            ->setHelp('选择该联系人所属的客户')
            ->setColumns(12)
        ;

        yield TextField::new('name', '姓名')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('联系人的真实姓名')
            ->setColumns(6)
        ;

        yield TextField::new('title', '职位')
            ->setMaxLength(100)
            ->setHelp('联系人在公司的职位')
            ->setColumns(6)
        ;

        // 联系方式标签页
        yield FormField::addTab('联系方式');

        yield EmailField::new('email', '邮箱')
            ->setHelp('联系人的邮箱地址，用于邮件沟通')
            ->setColumns(6)
        ;

        yield TelephoneField::new('phone', '电话')
            ->setHelp('固定电话号码')
            ->setColumns(3)
        ;

        yield TelephoneField::new('mobile', '手机')
            ->setHelp('移动电话号码')
            ->setColumns(3)
        ;

        // 状态与配置标签页
        yield FormField::addTab('状态配置');

        yield BooleanField::new('isPrimary', '主要联系人')
            ->setHelp('是否为该客户的主要联系人')
            ->setColumns(6)
        ;

        yield ChoiceField::new('status', '状态')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => ContactStatusEnum::class])
            ->setRequired(true)
            ->setHelp('联系人当前状态')
            ->formatValue(function ($value) {
                return $value instanceof ContactStatusEnum ? $value->getLabel() : '';
            })
            ->setColumns(6)
        ;

        // 系统信息标签页（仅查看页面显示）
        yield FormField::addTab('系统信息')->hideOnForm();

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('联系人信息创建时间')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('联系人信息最后更新时间')
        ;

        // 列表页显示字段
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id', 'ID')->setMaxLength(9999),
                AssociationField::new('customer', '客户')
                    ->formatValue(function ($value, Contact $entity): string {
                        $customer = $entity->getCustomer();

                        return null !== $customer
                            ? sprintf('%s (%s)', $customer->getName(), $customer->getCustomerCode())
                            : 'N/A';
                    }),
                TextField::new('name', '姓名'),
                TextField::new('title', '职位')
                    ->formatValue(fn (?string $value): string => $value ?? '无'),
                EmailField::new('email', '邮箱')
                    ->formatValue(fn (?string $value): string => $value ?? '无'),
                TelephoneField::new('mobile', '手机')
                    ->formatValue(fn (?string $value): string => $value ?? '无'),
                BooleanField::new('isPrimary', '主要联系人')
                    ->renderAsSwitch(false),
                ChoiceField::new('status', '状态')
                    ->formatValue(function (ContactStatusEnum $value): string {
                        return $value->getLabel();
                    }),
                DateTimeField::new('createTime', '创建时间')
                    ->setFormat('yyyy-MM-dd HH:mm:ss'),
            ];
        }

        // 详情页显示字段
        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addTab('基本信息'),
                IdField::new('id', 'ID'),
                AssociationField::new('customer', '客户'),
                TextField::new('name', '姓名'),
                TextField::new('title', '职位'),

                FormField::addTab('联系方式'),
                EmailField::new('email', '邮箱'),
                TelephoneField::new('phone', '电话'),
                TelephoneField::new('mobile', '手机'),

                FormField::addTab('状态配置'),
                BooleanField::new('isPrimary', '主要联系人'),
                ChoiceField::new('status', '状态')
                    ->formatValue(function (ContactStatusEnum $value): string {
                        return $value->getLabel();
                    }),

                FormField::addTab('系统信息'),
                DateTimeField::new('createTime', '创建时间')
                    ->setFormat('yyyy-MM-dd HH:mm:ss'),
                DateTimeField::new('updateTime', '更新时间')
                    ->setFormat('yyyy-MM-dd HH:mm:ss'),
            ];
        }
    }
}
