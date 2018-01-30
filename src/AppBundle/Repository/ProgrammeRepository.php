<?php

namespace AppBundle\Repository;

/**
 * ProgrammeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProgrammeRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Liste des programmes
     * dont la date est superieures a celle de la date encours
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @date: 11/11/2017 20:35
     */
    public function findProgrammeAdmin()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQuery('
                      SELECT p, d
                      FROM AppBundle:Programme p
                      LEFT JOIN p.departement d
                      WHERE p.datedeb >= :date
                      AND p.flag LIKE :flag
                      ORDER BY p.datedeb ASC
                '  )
                ->setParameters(array(
                    'date' => date('Y-m-d', time()),
                    'flag'  => '%valider%',
                ));
        ;
        return $qb->getResult();
    }

    /**
     * Liste des programmes non traiter
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @date: 11/11/2017 21:35
     */
    public function findTypeProgramme($flag)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQuery('
                      SELECT p, d
                      FROM AppBundle:Programme p
                      LEFT JOIN p.departement d
                      WHERE p.flag LIKE :flag
                      ORDER BY p.datedeb ASC
                '  )
                ->setParameter('flag', '%'.$flag.'%');
        ;
        return $qb->getResult();
    }

    /**
     * Liste des programmes non traiter lot de 10
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @date: 11/11/2017 22:02
     */
    public function findTypeProgrammeFiltre($flag, $offset, $limit)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQuery('
                      SELECT p, d
                      FROM AppBundle:Programme p
                      LEFT JOIN p.departement d
                      WHERE p.flag LIKE :flag
                      ORDER BY p.datedeb ASC
                '  )
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->setParameter('flag', '%'.$flag.'%');
        ;
        return $qb->getResult();
    }

    /**
     * Liste des $limit activités
     * dont la date est superieures a celle de la date encours
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @date: 12/11/2017 10:54
     */
    public function findActiviteLatest($offset, $limit)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQuery('
                      SELECT p, d
                      FROM AppBundle:Programme p
                      LEFT JOIN p.departement d
                      WHERE p.datedeb >= :date
                      ORDER BY p.datedeb ASC
                '  )
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->setParameter('date', date('Y-m-d', time()));
        ;
        return $qb->getResult();
    }

    /**
     * Calcul du nombre total d'activités enregistrées
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @since: 12/11/2017 11:23
     */
    public function countActiviteTotal()
    {
        $qb = $this->createQueryBuilder('p')
                   ->select('count(p.id)')
                   ->where('p.statut = 1')
                   ->andWhere('p.flag LIKE :flag')
                   ->setParameter('flag', '%valider%')
                   ->getQuery()->getSingleScalarResult();
        ;

        return $qb;
    }

    /**
     * Calcul du nombre total d'activités dediées au jeunes
     *
     * @author: Delrodie AMOIKON
     * @version: v1.0
     * @since: 12/11/2017 11:40
     */
    public function countActiviteJeune()
    {
        $qb = $this->createQueryBuilder('p')
                   ->select('count(p.id)')
                   ->where('p.statut = 1')
                   ->andWhere('
                       p.cible LIKE :scout
                       OR p.cible LIKE :jeune
                       OR p.cible LIKE :lou
                       OR p.cible LIKE :eclair
                       OR p.cible LIKE :chemin
                       OR p.cible LIKE :rout
                     ')
                    ->andWhere('p.cible LIKE :flag')
                   ->setParameters(array(
                       'scout' => '%scout%',
                       'jeune'  => '%jeune%',
                       'lou'  => '%lou%',
                       'eclair'  => '%eclair%',
                       'chemin'  => '%chemin%',
                       'rout'  => '%rout%',
                       'flag'   => '%valider%',
                   ))
                   ->getQuery()->getSingleScalarResult();
        ;

        return $qb;
    }

    /**
     * ================ DEPARTEMENT ==========================
     */



     /**
      * Liste des $limit activités
      * dont la date est superieures a celle de la date encours
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @date: 12/11/2017 10:54
      */
     public function findDepartementActiviteLatest($departement, $offset, $limit)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.departement = :departement
                       AND p.datedeb >= :date
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setFirstResult($offset)
                 ->setMaxResults($limit)
                 ->setParameters(array(
                    'date'  => date('Y-m-d', time()),
                    'departement' => $departement,
                 ));
         ;
         return $qb->getResult();
     }



     /**
      * Calcul du nombre total d'activités enregistrées
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @since: 12/11/2017 11:23
      */
     public function countDepartementActiviteTotal($district)
     {
         $qb = $this->createQueryBuilder('p')
                    ->select('count(p.id)')
                    ->where('p.statut = 1')
                    ->andWhere('p.departement = :departement')
                    ->setParameter('departement', $district)
                    ->getQuery()->getSingleScalarResult();
         ;

         return $qb;
     }

     /**
      * Calcul du nombre total d'activités dediées au jeunes
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @since: 12/11/2017 11:40
      */
     public function countDistrictActiviteJeune($departement)
     {
         $qb = $this->createQueryBuilder('p')
                    ->select('count(p.id)')
                    ->where('p.statut = 1')
                    ->where('p.departement = :departement')
                    ->andWhere('
                        p.cible LIKE :scout
                        OR p.cible LIKE :jeune
                        OR p.cible LIKE :lou
                        OR p.cible LIKE :eclair
                        OR p.cible LIKE :chemin
                        OR p.cible LIKE :rout
                      ')
                    ->setParameters(array(
                        'scout' => '%scout%',
                        'jeune'  => '%jeune%',
                        'lou'  => '%lou%',
                        'eclair'  => '%eclair%',
                        'chemin'  => '%chemin%',
                        'rout'  => '%rout%',
                        'departement'  => $departement,
                    ))
                    ->getQuery()->getSingleScalarResult();
         ;

         return $qb;
     }

     /**
      * Liste des programmes de departement
      * dont la date est superieures a celle de la date encours
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @date: 13/11/2017 18:12
      */
     public function findDepartementProgramme($departement)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.datedeb >= :date
                       AND p.departement = :departement
                       AND p.flag <> :flag
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setParameters(array(
                    'date'  => date('Y-m-d', time()),
                    'departement' => $departement,
                    'flag' => 'valider',
                 ));
         ;
         return $qb->getResult();
     }

     /**
      * Liste des programmes non traiter
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @date: 11/11/2017 21:35
      */
     public function findDepartementTypeProgramme($departement, $flag)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.flag LIKE :flag
                       AND p.departement = :departement
                       AND p.datedeb >= :date
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setParameters(array(
                    'flag'  => '%'.$flag.'%',
                    'departement' => $departement,
                    'date'  => date('Y-m-d', time()),
                 ));
         ;
         return $qb->getResult();
     }

     /**
      * Liste des programmes non traiter lot de 10
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @date: 11/11/2017 22:02
      */
     public function findDepartementTypeProgrammeFiltre($departement, $flag, $offset, $limit)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.flag LIKE :flag
                       AND p.departement = :departement
                       AND p.datedeb >= :date
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setFirstResult($offset)
                 ->setMaxResults($limit)
                 ->setParameters(array(
                    'flag'  => '%'.$flag.'%',
                    'departement' => $departement,
                    'date'  => date('Y-m-d', time()),
                 ));
         ;
         return $qb->getResult();
     }

     /**
      * Liste des programmes traites
      *
      * @author: Delrodie AMOIKON
      * @version: v1.0
      * @date: 11/11/2017 21:35
      */
     public function findProgrammeFinal($type, $flag)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.flag LIKE :flag
                       AND d.type LIKE :type
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setParameters(array(
                    'flag'  => '%'.$flag.'%',
                    'type' => '%'.$type.'%',
                 ));
         ;
         return $qb->getResult();
     }

     /**
      * verification de la date d'enegistrement
      *
      * @author: Delrodie AMOIKON
      * @version: v1.1
      * @date: 30/01/2018 06:21
      */
     public function verifProgrammeFinal($type, $flag, $debut, $fin)
     {
         $em = $this->getEntityManager();
         $qb = $em->createQuery('
                       SELECT p, d
                       FROM AppBundle:Programme p
                       LEFT JOIN p.departement d
                       WHERE p.flag LIKE :flag
                       AND d.type LIKE :type
                       AND ((p.datedeb <= :debut AND p.datefin >= :debut)
                       OR (p.datedeb >= :debut AND p.datedeb <= :fin)
                       OR (p.datefin >= :debut AND p.datefin <= :fin))
                       ORDER BY p.datedeb ASC
                 '  )
                 ->setParameters(array(
                    'flag'  => '%'.$flag.'%',
                    'type' => '%'.$type.'%',
                    'debut'  => $debut,
                    'fin'  => $fin,
                 ));
         ;
         return $qb->getResult();
     }
}
