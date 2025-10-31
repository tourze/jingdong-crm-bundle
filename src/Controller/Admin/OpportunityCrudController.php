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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 销售机会管理控制器
 *
 * @extends AbstractCrudController<Opportunity>
 */
#[AdminCrud(routePath: '/jingdong-crm/opportunity', routeName: 'jingdong_crm_opportunity')]
#[Autoconfigure(public: true)]
final class OpportunityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Opportunity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('销售机会')
            ->setEntityLabelInPlural('销售机会管理')
            ->setPageTitle('index', '销售机会列表')
            ->setPageTitle('detail', '销售机会详情')
            ->setPageTitle('new', '新建销售机会')
            ->setPageTitle('edit', '编辑销售机会')
            ->setHelp('index', '管理京东CRM系统中的销售机会，跟踪客户潜在需求和商业机会')
            ->setDefaultSort(['updateTime' => 'DESC'])
            ->setSearchFields(['name', 'opportunityCode', 'description', 'assignedTo', 'source'])
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

        yield TextField::new('opportunityCode', '机会编码')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('销售机会的唯一编码标识，用于系统内部识别')
        ;

        yield TextField::new('name', '机会名称')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('销售机会的名称描述')
        ;

        yield AssociationField::new('customer', '关联客户')
            ->setRequired(true)
            ->setHelp('该销售机会所属的客户')
            ->autocomplete()
            ->formatValue(function ($value, $entity) {
                if ($value instanceof Customer) {
                    return $value->getName() . ' [' . $value->getCustomerCode() . ']';
                }

                return $value;
            })
        ;

        yield TextareaField::new('description', '机会描述')
            ->setRequired(false)
            ->setMaxLength(2000)
            ->setHelp('详细描述销售机会的背景和需求')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 4],
            ])
        ;

        $stageField = EnumField::new('stage', '销售阶段');
        $stageField->setEnumCases(OpportunityStageEnum::cases());
        $stageField->setRequired(true);
        yield $stageField
            ->setHelp('当前销售机会所处的阶段')
            ->renderAsBadges([
                OpportunityStageEnum::IDENTIFY_NEEDS->value => 'primary',
                OpportunityStageEnum::SOLUTION_DESIGN->value => 'info',
                OpportunityStageEnum::BUSINESS_NEGOTIATION->value => 'warning',
                OpportunityStageEnum::CONTRACT_SIGNING->value => 'secondary',
                OpportunityStageEnum::CLOSED_WON->value => 'success',
                OpportunityStageEnum::CLOSED_LOST->value => 'danger',
            ])
        ;

        yield MoneyField::new('amount', '机会金额')
            ->setCurrency('CNY')
            ->setNumDecimals(2)
            ->setHelp('预估的销售机会金额（人民币）')
            ->hideOnIndex()
        ;

        yield NumberField::new('probability', '成交概率')
            ->setNumDecimals(0)
            ->setHelp('预估的成交概率，范围0-100（%）')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ])
            ->formatValue(function ($value) {
                return null !== $value && is_numeric($value) ? $value . '%' : null;
            })
        ;

        yield DateField::new('expectedCloseDate', '预期成交时间')
            ->setRequired(false)
            ->setHelp('预计完成成交的日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield TextField::new('assignedTo', '负责人')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setHelp('负责跟进该销售机会的人员')
        ;

        yield TextField::new('source', '机会来源')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setHelp('该销售机会的来源渠道')
            ->hideOnIndex()
        ;

        $statusField = EnumField::new('status', '状态');
        $statusField->setEnumCases(OpportunityStatusEnum::cases());
        $statusField->setRequired(true);
        yield $statusField
            ->setHelp('销售机会的当前状态')
            ->renderAsBadges([
                OpportunityStatusEnum::ACTIVE->value => 'success',
                OpportunityStatusEnum::WON->value => 'primary',
                OpportunityStatusEnum::LOST->value => 'danger',
            ])
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
            ->add(TextFilter::new('name', '机会名称'))
            ->add(TextFilter::new('opportunityCode', '机会编码'))
            ->add(EntityFilter::new('customer', '关联客户'))
            ->add(ChoiceFilter::new('stage', '销售阶段')
                ->setChoices($this->getOpportunityStageChoices()))
            ->add(ChoiceFilter::new('status', '状态')
                ->setChoices($this->getOpportunityStatusChoices()))
            ->add(NumericFilter::new('amount', '机会金额'))
            ->add(NumericFilter::new('probability', '成交概率'))
            ->add(TextFilter::new('assignedTo', '负责人'))
            ->add(TextFilter::new('source', '机会来源'))
            ->add(DateTimeFilter::new('expectedCloseDate', '预期成交时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function createEntity(string $entityFqcn): Opportunity
    {
        $opportunity = new Opportunity();

        // 设置默认值
        $opportunity->setOpportunityCode('');
        $opportunity->setName('');
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);

        return $opportunity;
    }

    /**
     * 获取销售机会阶段选项
     *
     * @return array<string, string>
     */
    private function getOpportunityStageChoices(): array
    {
        $choices = [];
        foreach (OpportunityStageEnum::cases() as $stage) {
            $choices[$stage->getLabel()] = $stage->value;
        }

        return $choices;
    }

    /**
     * 获取销售机会状态选项
     *
     * @return array<string, string>
     */
    private function getOpportunityStatusChoices(): array
    {
        $choices = [];
        foreach (OpportunityStatusEnum::cases() as $status) {
            $choices[$status->getLabel()] = $status->value;
        }

        return $choices;
    }
}
