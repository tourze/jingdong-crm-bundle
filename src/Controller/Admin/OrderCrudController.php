<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Enum\OrderStatus;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 订单管理控制器.
 *
 * @extends AbstractCrudController<Order>
 */
#[AdminCrud(routePath: '/jingdong-crm/order', routeName: 'jingdong_crm_order')]
#[Autoconfigure(public: true)]
final class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('订单')
            ->setEntityLabelInPlural('订单管理')
            ->setPageTitle('index', '订单列表')
            ->setPageTitle('detail', '订单详情')
            ->setPageTitle('new', '新建订单')
            ->setPageTitle('edit', '编辑订单')
            ->setHelp('index', '管理京东CRM系统中的订单信息')
            ->setDefaultSort(['orderDate' => 'DESC'])
            ->setSearchFields(['orderNumber', 'customer.name', 'jdOrderId', 'paymentMethod'])
            ->setPaginatorPageSize(20)
            ->showEntityActionsInlined()
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

        yield TextField::new('orderNumber', '订单号')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('订单的唯一编号，用于系统内部识别和跟踪')
            ->setColumns(6)
        ;

        yield AssociationField::new('customer', '客户')
            ->setRequired(true)
            ->setHelp('关联的客户信息')
            ->setColumns(6)
            ->formatValue(static function ($value) {
                if (!$value instanceof Customer) {
                    return '';
                }

                return sprintf('%s (%s)', $value->getName(), $value->getCustomerCode());
            })
        ;

        yield AssociationField::new('opportunity', '销售机会')
            ->setRequired(false)
            ->setHelp('关联的销售机会，可选')
            ->setColumns(6)
            ->formatValue(static function ($value) {
                if (!$value instanceof Opportunity) {
                    return '无';
                }

                return sprintf('%s (%s)', $value->getName(), $value->getOpportunityCode());
            })
            ->hideOnIndex()
        ;

        $statusField = EnumField::new('status', '订单状态');
        $statusField->setEnumCases(OrderStatus::cases());
        $statusField->setRequired(true);
        yield $statusField
            ->setHelp('订单的当前处理状态')
            ->setColumns(3)
            ->renderAsBadges([
                OrderStatus::PENDING_PAYMENT->value => 'warning',
                OrderStatus::PAID->value => 'info',
                OrderStatus::SHIPPING->value => 'primary',
                OrderStatus::COMPLETED->value => 'success',
                OrderStatus::CANCELLED->value => 'secondary',
                OrderStatus::REFUNDED->value => 'danger',
            ])
        ;

        yield MoneyField::new('totalAmount', '订单总金额')
            ->setRequired(true)
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setColumns(3)
            ->setHelp('订单的总金额（人民币）')
            ->setFormTypeOptions([
                'scale' => 2,
            ])
        ;

        yield MoneyField::new('paidAmount', '已付金额')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setColumns(3)
            ->setHelp('客户已支付的金额')
            ->setFormTypeOptions([
                'scale' => 2,
            ])
        ;

        yield MoneyField::new('discountAmount', '优惠金额')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setColumns(3)
            ->setHelp('订单的优惠减免金额')
            ->setFormTypeOptions([
                'scale' => 2,
            ])
            ->hideOnIndex()
        ;

        yield DateTimeField::new('orderDate', '下单日期')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns(6)
            ->setHelp('客户下单的日期和时间')
        ;

        yield TextField::new('paymentMethod', '支付方式')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setColumns(6)
            ->setHelp('客户选择的支付方式')
            ->hideOnIndex()
        ;

        yield TextareaField::new('shippingAddress', '收货地址')
            ->setRequired(false)
            ->setMaxLength(1000)
            ->setHelp('客户的收货地址信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 4],
            ])
            ->setColumns(12)
        ;

        yield TextField::new('jdOrderId', '京东订单ID')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setColumns(6)
            ->setHelp('京东系统中的订单标识符')
            ->hideOnIndex()
        ;

        yield TextareaField::new('notes', '备注')
            ->setRequired(false)
            ->setHelp('订单的备注信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 3],
            ])
            ->setColumns(12)
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns(6)
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setColumns(6)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('orderNumber', '订单号'))
            ->add(EntityFilter::new('customer', '客户'))
            ->add(EntityFilter::new('opportunity', '销售机会'))
            ->add(ChoiceFilter::new('status', '订单状态')
                ->setChoices($this->getOrderStatusChoices()))
            ->add(TextFilter::new('paymentMethod', '支付方式'))
            ->add(TextFilter::new('jdOrderId', '京东订单ID'))
            ->add(DateTimeFilter::new('orderDate', '下单日期'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function createEntity(string $entityFqcn): Order
    {
        $order = new Order();

        // 设置默认值
        $order->setStatus(OrderStatus::PENDING_PAYMENT);
        $order->setPaidAmount('0.00');
        $order->setDiscountAmount('0.00');

        return $order;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // 优化查询性能，预加载关联实体
        $queryBuilder
            ->leftJoin('entity.customer', 'customer')
            ->leftJoin('entity.opportunity', 'opportunity')
            ->addSelect('customer', 'opportunity')
            ->addOrderBy('entity.orderDate', 'DESC')
        ;

        return $queryBuilder;
    }

    /**
     * 获取订单状态选项.
     *
     * @return array<string, string>
     */
    private function getOrderStatusChoices(): array
    {
        $choices = [];
        foreach (OrderStatus::cases() as $status) {
            $choices[$status->getLabel()] = $status->value;
        }

        return $choices;
    }
}
