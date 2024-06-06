"use client"
import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import { jwtDecode } from 'jwt-decode';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

interface JwtPayload {
    username: string;
    sub: string;
    exp: number;
}

const Fine = () => {
    const router = useRouter();
    const { code } = useParams();
    const [fine, setFine] = useState<any>(null);
    const [currentUser, setCurrentUser] = useState<any>(null);
    const [fineNotFound, setFineNotFound] = useState<boolean>(false);

    useEffect(() => {
        const token = localStorage.getItem('token');
        if (!token || isTokenExpired(token)) { // Check for token expiration
            localStorage.removeItem('token'); // Remove the token
            router.push('/login'); // Redirect to the login page
        } else {
            const fetchFine = async () => {
                try {
                    const response = await fetch(`http://127.0.0.1:8000/api/fines?page=1&code=${code}`);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();
                    if (data['hydra:member'].length > 0) {
                        setFine(data['hydra:member'][0]);
                    } else {
                        setFineNotFound(true);
                    }
                } catch (error) {
                    console.error('Error fetching fine:', error);
                    setFineNotFound(true);
                }
            };

            if (code) {
                fetchFine();
            }

            if (token) {
                const decodedToken = jwtDecode<JwtPayload>(token);
                const email = decodedToken.username;
                getCurrentUser(email);
            }
        }
    }, [code]);

    const getCurrentUser = async (email: string) => {
        try {
            const response = await fetch(`http://127.0.0.1:8000/api/users?page=1&email=${email}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            if (data['hydra:member'].length > 0) {
                setCurrentUser(data['hydra:member'][0].id);
            } else {
                throw new Error('User not found');
            }
        } catch (error) {
            console.error('Error fetching current user:', error);
        }
    };

    const handlePayFine = async () => {
        if (fine && currentUser) {
            try {
                const response = await fetch(`http://127.0.0.1:8000/api/fines/${fine.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/ld+json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({
                        ...fine,
                        email: `/api/users/${currentUser}`,
                        pay: true
                    })
                });
                if (!response.ok) {
                    throw new Error('Failed to pay fine');
                }
                setFine({ ...fine, pay: true });
            } catch (error) {
                console.error('Error paying fine:', error);
            }
        }
    };

    const isTokenExpired = (token: string) => {
        const decodedToken = jwtDecode<JwtPayload>(token);
        return decodedToken.exp * 1000 < new Date().getTime();
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100 ">
            <div className="bg-white p-8 rounded-lg shadow-lg flex flex-col">
                {fineNotFound ? (
                    <div className="text-center">
                        <p className="text-red-500 mb-4">Cette amende n'existe pas</p>
                        <Link href="/" className="mt-6 bg-blue-500 text-white px-4 py-2 rounded text-center">Retour au profil</Link>
                    </div>
                ) : fine ? (
                    <div className="space-y-4">
                        <div>
                            <p className="block text-gray-700 mb-2">Amende numéro : {fine.code}</p>
                        </div>
                        <div>
                            <p className="block text-gray-700 mb-2">Raison de l'amende : {fine.name}</p>
                        </div>
                        <div>
                            <p className="block text-gray-700 mb-2">Description : {fine.description}</p>
                        </div>
                        <div>
                            <p className="block text-gray-700 mb-2">Prix : {fine.value}</p>
                        </div>
                        {!fine?.pay && (
                            <button onClick={handlePayFine} className="mt-6 bg-blue-500 text-white px-4 py-2 rounded">Payer</button>
                        )}
                        {fine?.pay && (
                            <p className="mt-6 text-green-500">Cette amende a déjà été payée</p>
                        )}
                    </div>
                ) : (
                    <p className="text-center">Chargement...</p>
                )}
            </div>
        </div>
    );
};

export default Fine;
