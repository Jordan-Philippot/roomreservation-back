<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

use App\Repository\ApartmentRepository;
use App\Repository\RoomRepository;

use App\Entity\Room;


class RoomController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("room/get_all", name="room_get_all")
     */
    public function getRooms(RoomRepository $roomRepository)
    {
        $rooms = $roomRepository->findAll();
        $listApartments = [];
        $listRooms = [];

        foreach ($rooms as $room) {

            foreach ($room->getApartment() as $apartment) {
                $listApartments[] = [
                    'id' => $apartment->getId(),
                    'name' => $apartment->getName(),
                    'street' => $apartment->getStreet(),
                    'zipCode' => $apartment->getZipCode(),
                    'city' => $apartment->getCity(),
                    'rooms' => $listRooms,
                ];
            }

            $listRooms[] = [
                'id' => $room->getId(),
                'number' => $room->getNumber(),
                'area' => $room->getArea(),
                'price' => $room->getPrice(),
                'apartments' => $listApartments,
            ];
        }
        return $this->json([
            'rooms' => $listRooms,
        ]);
    }

    /**
     * @Route("room/{id}", name="room_get_one")
     */
    public function getRoom(RoomRepository $roomRepository, $id)
    {
        $room = $roomRepository->findOneBy(['id' => $id]);

        dump($room);
        $listRooms = [];
        $listApartments = [];

        if ($room) {
            $listApartments[] = [
                'id' => $room->getApartment()->getId(),
                'name' => $room->getApartment()->getName(),
                'street' => $room->getApartment()->getStreet(),
                'zipCode' => $room->getApartment()->getZipCode(),
                'city' => $room->getApartment()->getCity(),
            ];


            $listRoom[] = [
                'id' => $room->getId(),
                'number' => $room->getNumber(),
                'area' => $room->getArea(),
                'price' => $room->getPrice(),
                'apartments' => $listApartments,
            ];
        }

        return $this->json([
            'room' => $listRoom,
        ]);
    }


    /**
     * @Route("room/update/{id}", name="room_update")
     */
    public function updateRoom(RoomRepository $roomRepository, $id, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $errors = [];

        $number = htmlspecialchars($data['number']);
        $area = htmlspecialchars($data['area']);
        $price = htmlspecialchars($data['price']);

        $room = $roomRepository->findOneBy(['id' => $id]);

        if (!$room) {
            $errors['room'] = "Aucune chambre n'a été trouvée";
        }
        if (!is_numeric($number) || empty($number) || !$number) {
            $errors['number'] = "Veuillez renseigner un numéro de chambre valide";
        }
        if (!is_string($area) || empty($area) || !$area) {
            $errors['area'] = "Veuillez renseigner une surface valide";
        }
        if (!is_string($price) || empty($price) || !$price) {
            $errors['price'] = "Veuillez renseigner un code postale valide";
        }



        if (empty($errors)) {
            $room->setNumber($number);
            $room->setArea($area);
            $room->setPrice($price);

            $em = $this->doctrine->getManager();
            $em->persist($room);
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
     * @Route("room/delete/{id}", name="room_delete")
     */
    public function deleteRoom(RoomRepository $roomRepository, $id)
    {
        $room = $roomRepository->findOneBy(['id' => $id]);

        if (!empty($id) && $room) {
            $em = $this->doctrine->getManager();
            $em->remove($room);
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
     * @Route("create/room/{id}", name="room_create")
     */
    public function createRoom(Request $request, $id, ApartmentRepository $apartmentRepository)
    {
        $data = json_decode($request->getContent(), true);

        $apart = $apartmentRepository->findOneBy(['id' => $id]);


        dump($data);

        $errors = [];

        $number = $data['number'];
        $area = $data['area'];
        $price = $data['price'];

        if (!$apart) {
            $errors['apart'] = "Aucun appartement n'a été trouvé";
        }

        if (!is_string($number) || empty($number) || !$number) {
            $errors['number'] = "Veuillez renseigner un N° de chambre valide";
        }
        if (!is_string($area) || empty($area) || !$area) {
            $errors['area'] = "Veuillez renseigner une surface valide";
        }
        if (!is_string($price) || empty($price) || !$price) {
            $errors['price'] = "Veuillez renseigner un prix valide";
        }



        if (empty($errors)) {

            $room = new Room();
            $room->setNumber($number);
            $room->setArea($area);
            $room->setPrice($price);
            $room->setApartment($apart);

            $em = $this->doctrine->getManager();
            $em->persist($room);
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
