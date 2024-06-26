"use client"
import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import {jwtDecode} from 'jwt-decode';

interface JwtPayload {
  username: string;
  exp: number;
}

interface UserData {
  id: string;
  email: string;
  name: string;
  firstname: string;
  address: string;
  phone: string;
  card: string;
  crypto: string;
  expiry: string;
}

interface Fine {
  value: number;
  id: string;
  name: string;
  description: string;
}

const isValidCardNumber = (cardNumber: string): boolean => {
  let sum = 0;
  let shouldDouble = false;

  for (let i = cardNumber.length - 1; i >= 0; i--) {
    let digit = parseInt(cardNumber.charAt(i));

    if (shouldDouble) {
      digit *= 2;
      if (digit > 9) digit -= 9;
    }

    sum += digit;
    shouldDouble = !shouldDouble;
  }

  return sum % 10 === 0;
};

const isValidFineNumber = (fineNumber: string): boolean => {
  const currentYear = new Date().getFullYear().toString();
  const regex = new RegExp(`^(?:(?=([A-Y])([A-Z]))\\1\\2)${currentYear}_(\\d{1,2})_(\\d{1,2})$`);
  const match = regex.exec(fineNumber);
  if (match) {
    const firstDigitGroup = parseInt(match[3]);
    const secondDigitGroup = parseInt(match[4]);
    return firstDigitGroup + secondDigitGroup === 100;
  }
  return false;
};

const Home = () => {
  const router = useRouter();
  const [userData, setUserData] = useState<UserData>({
    id: '',
    email: '',
    name: '',
    firstname: '',
    address: '',
    phone: '',
    card: '',
    crypto: '',
    expiry: '',
  });

  const [message, setMessage] = useState('');
  const [errors, setErrors] = useState<{
    name?: string;
    firstname?: string;
    address?: string;
    phone?: string;
    card?: string;
    crypto?: string;
    expiry?: string;
  }>({});

  const [fines, setFines] = useState<Fine[]>([]);
  const [searchResult, setSearchResult] = useState<Fine | null>(null);

  const fetchFines = async (email: string, token: string) => {
    try {
      const response = await fetch(`http://localhost:8000/api/fines?page=1&email=${email}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      const data = await response.json();
      setFines(data['hydra:member']);
    } catch (error) {
      console.error('Error fetching fines:', error);
    }
  };

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return;
    }
    
    const decodedToken = jwtDecode<JwtPayload>(token);
    const email = decodedToken.username;
  
    const fetchData = async () => {
      try {
        const response = await fetch(`http://localhost:8000/api/users?page=1&email=${email}`, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        const data = await response.json();
        if (data['hydra:member'].length > 0) {
          setUserData(data['hydra:member'][0]);
          fetchFines(data['hydra:member'][0].id, token);
        }
      } catch (error) {
        console.error('Error fetching user data:', error);
        router.push('/login');
      }
    };
    
    fetchData();
  }, [router]);

  const handleLogout = () => {
    localStorage.removeItem('token');
    router.push('/login');
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setUserData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
    setErrors({
      ...errors,
      [name]: '',
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    let valid = true;
    const newErrors: typeof errors = {};

    if (userData.name.length < 2) {
      newErrors.name = 'Le nom doit contenir au moins 2 caractères.';
      valid = false;
    }

    if (userData.firstname.length < 2) {
      newErrors.firstname = 'Le prénom doit contenir au moins 2 caractères.';
      valid = false;
    }

    if (userData.address.length < 5) {
      newErrors.address = 'L\'adresse doit contenir au moins 5 caractères.';
      valid = false;
    }

    const phoneRegex = /^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/;
    if (!phoneRegex.test(userData.phone)) {
      newErrors.phone = 'Veuillez entrer un numéro de téléphone français valide (ex: 0601020304).';
      valid = false;
    }

    if (!isValidCardNumber(userData.card)) {
      newErrors.card = 'Le numéro de carte est invalide.';
      valid = false;
    }

    if (!Number.isInteger(parseInt(userData.crypto)) || userData.crypto.length !== 3) {
      newErrors.crypto = 'Le cryptogramme visuel doit être un entier composé de trois chiffres.';
      valid = false;
    }

    const expiryRegex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear() % 100;
    const currentMonth = currentDate.getMonth() + 1;
    const [enteredMonth, enteredYear] = userData.expiry.split('/').map((value) => parseInt(value));

    if (
      !expiryRegex.test(userData.expiry) ||
      (enteredYear < currentYear) ||
      (enteredYear === currentYear && enteredMonth < currentMonth)
    ) {
      newErrors.expiry = 'Veuillez entrer une date d\'expiration valide (ex: 10/26).';
      valid = false;
    }

    if (!valid) {
      setErrors(newErrors);
      return;
    }

    const token = localStorage.getItem('token');
    if (token) {
      try {
        const response = await fetch(`http://localhost:8000/api/users/${userData.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/ld+json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(userData)
        });

        if (response.ok) {
          const data = await response.json();
          setUserData(data);
          setMessage('Données utilisateur mises à jour avec succès');
        } else {
          throw new Error('Failed to update user data');
        }
      } catch (error) {
        console.error('Error updating user data:', error);
        setMessage('Échec de la mise à jour des données utilisateur');
      }
    }
  };

  useEffect(() => {
    const token = localStorage.getItem('token');
    if (token) {
      try {
        const decodedToken = jwtDecode<JwtPayload>(token);

        if (decodedToken.exp * 1000 < new Date().getTime()) {
          localStorage.removeItem('token');
          router.push('/login');
        }

      } catch (error) {
        console.log('TOKEN BROKEN');
        localStorage.removeItem('token');
        router.push('/login');
      }
    } else {
      router.push('/login');
    }
  }, [router]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="bg-white p-8 rounded-lg shadow-lg">
        <h1 className="text-2xl font-bold mb-4">Bienvenue sur la page d'accueil</h1>
        <p className="text-gray-700 mb-4">Vous êtes connecté avec succès.</p>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-gray-700">Mail</label>
            <p className="w-full px-4 py-2 border rounded">{userData.email}</p>
          </div>
          <div>
            <label className="block text-gray-700">Name</label>
            <input type="text" name="name" value={userData.name} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.name && <p className="text-red-500">{errors.name}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Firstname</label>
            <input type="text" name="firstname" value={userData.firstname} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.firstname && <p className="text-red-500">{errors.firstname}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Address</label>
            <input type="text" name="address" value={userData.address} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.address && <p className="text-red-500">{errors.address}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Phone</label>
            <input type="text" name="phone" value={userData.phone} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.phone && <p className="text-red-500">{errors.phone}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Card</label>
            <input type="text" name="card" value={userData.card} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.card && <p className="text-red-500">{errors.card}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Crypto</label>
            <input type="text" name="crypto" value={userData.crypto} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.crypto && <p className="text-red-500">{errors.crypto}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Expiry</label>
            <input type="text" name="expiry" value={userData.expiry} onChange={handleChange} className="w-full px-4 py-2 border rounded" />
            {errors.expiry && <p className="text-red-500">{errors.expiry}</p>}
          </div>
          <button type="submit" className="mt-6 bg-blue-500 text-white px-4 py-2 rounded">Mettre à jour</button>
        </form>

        <button onClick={handleLogout} className="mt-6 bg-red-500 text-white px-4 py-2 rounded">Se déconnecter</button>
        {message && <p className="text-green-500 mt-4">{message}</p>}
        
        <form onSubmit={(e) => { 
          e.preventDefault();
          const token = localStorage.getItem('token');
          const fineNumber = (e.target as HTMLFormElement).fineNumber.value;
          if (token && isValidFineNumber(fineNumber)) {
            router.push(`/fine/${fineNumber}`);
          } else {
            setMessage('Numéro d\'amende invalide.');
          }
        }}>
          <div>
            <h2>Rechercher une amende</h2>
            <input name="fineNumber" className="w-full px-4 py-2 border rounded" />
            <button type="submit" className="mt-6 bg-blue-500 text-white px-4 py-2 rounded">Rechercher</button>
          </div>
        </form>
        
        {searchResult && (
          <div>
            <h2 className="text-2xl font-bold mb-4">Résultat de la recherche</h2>
            <p>{searchResult.name} - {searchResult.description} - {searchResult.value} €</p>
          </div>
        )}
        
        <h2 className="text-2xl font-bold mb-4">Amendes</h2>
        <ul>
          {fines.map((fine) => (
            <li key={fine.id}>
              {fine.name} - {fine.description} - {fine.value} €
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
};

export default Home;
