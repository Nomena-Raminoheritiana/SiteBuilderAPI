<?php 

namespace App\Validator\Constraints;

use App\Entity\Model;
use App\Validator\Constraints\UniqueUrlPerParent;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use UnexpectedValueException;

class UniqueUrlPerParentValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUrlPerParent) {
            throw new UnexpectedTypeException($constraint, UniqueUrlPerParent::class);
        }

        if (!$value instanceof Model) {
            throw new UnexpectedValueException($value, Model::class);
        }

        $url = $value->getUrl();
        if (!$url) return;

        $qb = $this->em->getRepository(Model::class)->createQueryBuilder('m');
        $qb->where('m.url = :url')
            ->andWhere('m.parent = :parent OR m.id = :parent') // 1. même parent OU 2. url identique au parent lui-même
            ->setParameter('url', $url)
            ->setParameter('parent', $value->getParent()?->getId());

        if ($value->getId()) {
            $qb->andWhere('m.id != :id')
                ->setParameter('id', $value->getId());
        }

        $existing = $qb->getQuery()->getOneOrNullResult();

        if ($existing) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ url }}', $url)
                ->atPath('url')
                ->addViolation();
        }
    }

}
