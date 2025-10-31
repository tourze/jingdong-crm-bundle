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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Enum\LeadSource;
use JingdongCrmBundle\Enum\LeadStatus;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 销售线索管理控制器
 *
 * @extends AbstractCrudController<Lead>
 */
#[AdminCrud(routePath: '/jingdong-crm/lead', routeName: 'jingdong_crm_lead')]
#[Autoconfigure(public: true)]
final class LeadCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lead::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('销售线索')
            ->setEntityLabelInPlural('销售线索管理')
            ->setPageTitle('index', '销售线索列表')
            ->setPageTitle('detail', '销售线索详情')
            ->setPageTitle('new', '新建销售线索')
            ->setPageTitle('edit', '编辑销售线索')
            ->setHelp('index', '管理京东CRM系统中的销售线索信息')
            ->setDefaultSort(['updateTime' => 'DESC'])
            ->setSearchFields(['leadCode', 'companyName', 'contactName', 'email', 'phone', 'assignedTo'])
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

        yield TextField::new('leadCode', '线索编码')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('线索的唯一编码标识，用于系统内部识别')
        ;

        yield TextField::new('companyName', '公司名称')
            ->setRequired(true)
            ->setMaxLength(200)
            ->setHelp('潜在客户的公司名称')
        ;

        yield TextField::new('contactName', '联系人姓名')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('主要联系人的姓名')
        ;

        yield TextField::new('title', '职位')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setHelp('联系人在公司的职位')
            ->hideOnIndex()
        ;

        yield EmailField::new('email', '邮箱')
            ->setRequired(false)
            ->setHelp('联系人的邮箱地址')
            ->hideOnIndex()
        ;

        yield TelephoneField::new('phone', '电话')
            ->setRequired(false)
            ->setHelp('联系人的电话号码')
        ;

        $sourceField = EnumField::new('source', '线索来源');
        $sourceField->setEnumCases(LeadSource::cases());
        $sourceField->setRequired(true);
        yield $sourceField
            ->setHelp('线索的获取来源渠道')
            ->renderAsBadges([
                LeadSource::WEBSITE->value => 'primary',
                LeadSource::PHONE->value => 'info',
                LeadSource::ADVERTISEMENT->value => 'warning',
                LeadSource::REFERRAL->value => 'success',
                LeadSource::OTHER->value => 'secondary',
            ])
        ;

        $statusField = EnumField::new('status', '线索状态');
        $statusField->setEnumCases(LeadStatus::cases());
        $statusField->setRequired(true);
        yield $statusField
            ->setHelp('当前线索的处理状态')
            ->renderAsBadges([
                LeadStatus::NEW->value => 'primary',
                LeadStatus::IN_PROGRESS->value => 'warning',
                LeadStatus::CONVERTED->value => 'success',
                LeadStatus::CLOSED->value => 'danger',
            ])
        ;

        yield NumberField::new('score', '评分')
            ->setRequired(false)
            ->setHelp('线索质量评分（0-100分）')
            ->setFormTypeOptions([
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ])
            ->hideOnIndex()
        ;

        yield TextareaField::new('notes', '备注')
            ->setRequired(false)
            ->setHelp('关于此线索的详细备注信息')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => ['rows' => 4],
            ])
        ;

        yield TextField::new('assignedTo', '分配给')
            ->setRequired(false)
            ->setMaxLength(100)
            ->setHelp('负责跟进此线索的销售人员')
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
            ->add(TextFilter::new('leadCode', '线索编码'))
            ->add(TextFilter::new('companyName', '公司名称'))
            ->add(TextFilter::new('contactName', '联系人姓名'))
            ->add(ChoiceFilter::new('source', '线索来源')
                ->setChoices($this->getLeadSourceChoices()))
            ->add(ChoiceFilter::new('status', '线索状态')
                ->setChoices($this->getLeadStatusChoices()))
            ->add(NumericFilter::new('score', '评分'))
            ->add(TextFilter::new('assignedTo', '分配给'))
            ->add(TextFilter::new('email', '邮箱'))
            ->add(TextFilter::new('phone', '电话'))
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

    public function createEntity(string $entityFqcn): Lead
    {
        $lead = new Lead();

        // 设置默认值
        $lead->setLeadCode(''); // 必填字段，表单中用户需要填写
        $lead->setCompanyName(''); // 必填字段，表单中用户需要填写
        $lead->setContactName(''); // 必填字段，表单中用户需要填写
        $lead->setSource(LeadSource::WEBSITE); // 设置默认来源
        $lead->setStatus(LeadStatus::NEW);

        return $lead;
    }

    /**
     * 获取线索来源选项
     *
     * @return array<string, string>
     */
    private function getLeadSourceChoices(): array
    {
        $choices = [];
        foreach (LeadSource::cases() as $source) {
            $choices[$source->getLabel()] = $source->value;
        }

        return $choices;
    }

    /**
     * 获取线索状态选项
     *
     * @return array<string, string>
     */
    private function getLeadStatusChoices(): array
    {
        $choices = [];
        foreach (LeadStatus::cases() as $status) {
            $choices[$status->getLabel()] = $status->value;
        }

        return $choices;
    }
}
