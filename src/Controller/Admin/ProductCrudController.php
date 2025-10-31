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
use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 京东CRM产品管理控制器
 * @extends AbstractCrudController<Product>
 */
#[AdminCrud(routePath: '/jingdong-crm/product', routeName: 'jingdong_crm_product')]
#[Autoconfigure(public: true)]
final class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('产品')
            ->setEntityLabelInPlural('产品管理')
            ->setPageTitle('index', '产品列表')
            ->setPageTitle('new', '新增产品')
            ->setPageTitle('edit', '编辑产品')
            ->setPageTitle('detail', '产品详情')
            ->setHelp('index', '管理京东CRM系统的产品信息')
            ->setSearchFields(['productCode', 'name', 'category', 'jdProductId'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(30)
            ->showEntityActionsInlined()
            ->setFormOptions([
                'validation_groups' => ['Default'],
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        // 构建产品状态选择项
        $statusChoices = [];
        foreach (ProductStatus::cases() as $case) {
            $statusChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('productCode', '产品编码'))
            ->add(TextFilter::new('name', '产品名称'))
            ->add(TextFilter::new('category', '产品分类'))
            ->add(
                ChoiceFilter::new('status', '产品状态')
                    ->setChoices($statusChoices)
            )
            ->add(TextFilter::new('jdProductId', '京东产品ID'))
            ->add(TextFilter::new('createdBy', '创建人'))
            ->add(TextFilter::new('updatedBy', '修改人'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
            ->setHelp('产品记录唯一标识')
        ;

        yield from $this->getBasicFields($pageName);
        yield from $this->getPriceFields($pageName);
        yield from $this->getStatusFields($pageName);
        yield from $this->getDescriptionFields($pageName);
        yield from $this->getJdFields($pageName);
        yield from $this->getTimestampFields($pageName);
        yield from $this->getBlameableFields($pageName);
    }

    /**
     * 基础字段配置
     * @return iterable<FieldInterface>
     */
    private function getBasicFields(string $pageName): iterable
    {
        // 产品编码
        yield TextField::new('productCode', '产品编码')
            ->setColumns(3)
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('产品的唯一编码，不可重复')
        ;

        // 产品名称
        yield TextField::new('name', '产品名称')
            ->setColumns(6)
            ->setRequired(true)
            ->setMaxLength(200)
            ->setHelp('产品的完整名称')
        ;

        // 产品分类
        yield TextField::new('category', '产品分类')
            ->setColumns(3)
            ->setMaxLength(100)
            ->setHelp('产品所属分类')
        ;

        // 产品单位
        yield TextField::new('unit', '计量单位')
            ->setColumns(2)
            ->setMaxLength(50)
            ->setHelp('产品的计量单位，如：件、个、套等')
            ->hideOnIndex()
        ;
    }

    /**
     * 价格字段配置
     * @return iterable<FieldInterface>
     */
    private function getPriceFields(string $pageName): iterable
    {
        // 产品价格
        yield MoneyField::new('price', '产品价格')
            ->setColumns(3)
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->setRequired(true)
            ->setHelp('产品的销售价格（人民币）')
        ;
    }

    /**
     * 状态字段配置
     * @return iterable<FieldInterface>
     */
    private function getStatusFields(string $pageName): iterable
    {
        // 产品状态
        $statusField = EnumField::new('status', '产品状态');
        $statusField->setColumns(2);
        $statusField->setEnumCases(ProductStatus::cases());
        $statusField->setRequired(true);
        yield $statusField
            ->setHelp('产品的当前状态')
            ->renderAsBadges([
                'on_sale' => 'success',
                'off_shelf' => 'warning',
                'out_of_stock' => 'danger',
            ])
        ;
    }

    /**
     * 描述字段配置
     * @return iterable<FieldInterface>
     */
    private function getDescriptionFields(string $pageName): iterable
    {
        // 产品描述
        yield TextareaField::new('description', '产品描述')
            ->setColumns(12)
            ->setMaxLength(5000)
            ->setHelp('产品的详细描述信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => [
                    'rows' => 6,
                    'placeholder' => '请输入产品的详细描述...',
                ],
            ])
        ;
    }

    /**
     * 京东相关字段配置
     * @return iterable<FieldInterface>
     */
    private function getJdFields(string $pageName): iterable
    {
        // 京东产品ID
        yield TextField::new('jdProductId', '京东产品ID')
            ->setColumns(4)
            ->setMaxLength(100)
            ->setHelp('对应的京东平台产品ID')
            ->hideOnIndex()
        ;
    }

    /**
     * 时间戳字段配置
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields(string $pageName): iterable
    {
        // 只在详情页面显示时间戳字段
        if (Crud::PAGE_DETAIL !== $pageName) {
            return;
        }

        // 创建时间
        yield DateTimeField::new('createTime', '创建时间')
            ->setColumns(3)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('产品记录的创建时间')
            ->onlyOnDetail()
        ;

        // 更新时间
        yield DateTimeField::new('updateTime', '更新时间')
            ->setColumns(3)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('产品记录的最后更新时间')
            ->onlyOnDetail()
        ;
    }

    /**
     * 责任人字段配置
     * @return iterable<FieldInterface>
     */
    private function getBlameableFields(string $pageName): iterable
    {
        // 只在详情页面显示责任人字段
        if (Crud::PAGE_DETAIL !== $pageName) {
            return;
        }

        // 创建人
        yield TextField::new('createdBy', '创建人')
            ->setColumns(3)
            ->setHelp('创建该产品记录的用户标识')
            ->onlyOnDetail()
        ;

        // 更新人
        yield TextField::new('updatedBy', '更新人')
            ->setColumns(3)
            ->setHelp('最后更新该产品记录的用户标识')
            ->onlyOnDetail()
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // 设置默认排序
        $queryBuilder->addOrderBy('entity.id', 'DESC');

        return $queryBuilder;
    }
}
