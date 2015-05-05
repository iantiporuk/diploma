<?php
namespace BSUIR\IndividualPlanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BSUIR\IndividualPlanBundle\Form\Type\ProfessorsType;
use BSUIR\IndividualPlanBundle\Entity\Professors;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ProfessorsController extends Controller
{
    /**
     * registration form
     *
     * @Route("/register", name="professors_register")
     * @Method({"GET","POST"})
     */
    public function registerAction(Request $request)
    {
        $professor = new Professors();
        $form = $this->createForm(new ProfessorsType(), $professor);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($professor);
            $password = $encoder->encodePassword($professor->getPassword(), $professor->getSalt());
            $professor->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            try{
                $em->persist($professor);
                $em->flush();

                $token = new UsernamePasswordToken($professor, null, 'local', $professor->getRoles());
                $this->get('security.context')->setToken($token);
                $this->get('session')->set('_security_secured_area',serialize($token));
            } catch( \Exception $e) {
                throw new \Exception($e->getMessage());
            }

            return $this->redirect($this->generateUrl('bsuir_individual_plan_homepage'));
        }


        return $this->render('BSUIRIndividualPlanBundle:Professors:register.html.twig',
            array('form' => $form->createView())
        );
    }
}
