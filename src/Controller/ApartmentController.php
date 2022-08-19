<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Repository\ApartmentRepository;
use App\Entity\Apartment;


class ApartmentController extends AbstractController
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("apartment/get_all", name="apartment_get_all")
     */
    public function getApartments(ApartmentRepository $apartmentRepository)
    {
        $apartments = $apartmentRepository->findAll();
        $listApartments = [];
        $listRooms = [];

        foreach ($apartments as $apartment) {

            foreach ($apartment->getRooms() as $room) {
                $listRooms[] = [
                    'id' => $room->getId(),
                    'number' => $room->getNumber(),
                    'area' => $room->getArea(),
                    'price' => $room->getPrice(),
                ];
            }

            $listApartments[] = [
                'id' => $apartment->getId(),
                'name' => $apartment->getName(),
                'street' => $apartment->getStreet(),
                'zipCode' => $apartment->getZipCode(),
                'city' => $apartment->getCity(),
                'rooms' => $listRooms,
            ];
        }
        return $this->json([
            'apartments' => $listApartments,
        ]);
    }

    /**
     * @Route("apartment/{id}", name="apartment_get_one")
     */
    public function getApartment(ApartmentRepository $apartmentRepository, $id)
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $id]);

        $listRooms = [];
        $apartmentArr = [];

        if ($apartment) {
            foreach ($apartment->getRooms() as $room) {
                $listRooms[] = [
                    'id' => $room->getId(),
                    'number' => $room->getNumber(),
                    'area' => $room->getArea(),
                    'price' => $room->getPrice(),
                ];
            }

            $apartmentArr[] = [
                'id' => $apartment->getId(),
                'name' => $apartment->getName(),
                'street' => $apartment->getStreet(),
                'zipCode' => $apartment->getZipCode(),
                'city' => $apartment->getCity(),
                'rooms' => $listRooms,
            ];
        }

        return $this->json([
            'apartment' => $apartmentArr,
        ]);
    }


    /**
     * @Route("apartment/update/{id}", name="apartment_update")
     */
    public function updateApartment(ApartmentRepository $apartmentRepository, $id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        dump($request);

        $errors = [];

        $name = $data['name'];
        $street = $data['street'];
        $zipCode = $data['zipCode'];
        $city = $data['city'];

        $apartment = $apartmentRepository->findOneBy(['id' => $id]);

        if (!is_string($name) || empty($name) || !$name) {
            $errors['name'] = "Veuillez renseigner un nom d'appartement valide";
        }
        if (!is_string($street) || empty($street) || !$street) {
            $errors['street'] = "Veuillez renseigner une rue valide";
        }
        if (!is_string($zipCode) || empty($zipCode) || !$zipCode) {
            $errors['zipCode'] = "Veuillez renseigner un code postale valide";
        }
        if (!is_string($city) || empty($city) || !$city) {
            $errors['city'] = "Veuillez renseigner une ville valide";
        }


        if (empty($errors)) {
            $apartment->setName($name);
            $apartment->setStreet($street);
            $apartment->setZipCode($zipCode);
            $apartment->setCity($city);

            $em = $this->doctrine->getManager();
            $em->persist($apartment);
            $em->flush();

            return $this->json([
                'response' => true
            ]);
        } else {
            return $this->json([
                'errors' => $errors
            ]);
        }

        return $this->json([
            'response' => true,
        ]);
    }


    /**
     * @Route("apartment/delete/{id}", name="apartment_delete")
     */
    public function deleteApartment(ApartmentRepository $apartmentRepository, $id)
    {
        $apartment = $apartmentRepository->findOneBy(['id' => $id]);

        if (!empty($id) && $apartment) {
            $em = $this->doctrine->getManager();
            $em->remove($apartment);
            $em->flush();


            return $this->json([
                'response' => true
            ]);
        } else {
            return $this->json([
                'errors' => "Aucun appartement n'a été trouvé"
            ]);
        }

        return $this->json([
            'response' => true,
        ]);
    }



     /**
     * @Route("create/apartment", name="apartment_create")
     */
    public function createApartment(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        dump($data);

        $errors = [];

        $name = $data['name'];
        $street = $data['street'];
        $zipCode = $data['zipCode'];
        $city = $data['city'];


        if (!is_string($name) || empty($name) || !$name) {
            $errors['name'] = "Veuillez renseigner un nom d'appartement valide";
        }
        if (!is_string($street) || empty($street) || !$street) {
            $errors['street'] = "Veuillez renseigner une rue valide";
        }
        if (!is_string($zipCode) || empty($zipCode) || !$zipCode) {
            $errors['zipCode'] = "Veuillez renseigner un code postale valide";
        }
        if (!is_string($city) || empty($city) || !$city) {
            $errors['city'] = "Veuillez renseigner une ville valide";
        }


        if (empty($errors)) {

            $apartment = new Apartment();
            $apartment->setName($name);
            $apartment->setStreet($street);
            $apartment->setZipCode($zipCode);
            $apartment->setCity($city);

            $em = $this->doctrine->getManager();
            $em->persist($apartment);
            $em->flush();

            return $this->json([
                'response' => true
            ]);
        } else {
            return $this->json([
                'errors' => $errors
            ]);
        }

        return $this->json([
            'response' => true,
        ]);
    }
}
