"use client"
import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

const SignUpForm = () => {
  const router = useRouter();

  useEffect(() => {
    if (localStorage.getItem('token')) {
      router.push('/');
    }
  }, []);

  const [formData, setFormData] = useState({
    email: '',
    password: '',
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
    email?: string;
    password?: string;
    name?: string;
    firstname?: string;
    address?: string;
    phone?: string;
    card?: string;
    crypto?: string;
    expiry?: string;
  }>({});

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
    setErrors({
      ...errors,
      [name]: '',
    });
  };

  const luhnValidation = (number: string) => {
    const digits = number.split('').map(Number);

    for (let i = digits.length - 2; i >= 0; i -= 2) {
      digits[i] *= 2;
      if (digits[i] > 9) {
        digits[i] -= 9;
      }
    }

    const total = digits.reduce((acc, curr) => acc + curr, 0);
    return total % 10 === 0;
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    let valid = true;
    const newErrors: typeof errors = {};

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email || !emailRegex.test(formData.email)) {
      newErrors.email = 'Veuillez entrer une adresse e-mail valide.';
      valid = false;
    }

    if (formData.name.length < 2) {
      newErrors.name = 'Le nom doit contenir au moins 2 caractères.';
      valid = false;
    }

    if (formData.firstname.length < 2) {
      newErrors.firstname = 'Le prénom doit contenir au moins 2 caractères.';
      valid = false;
    }

    if (formData.address.length < 5) {
      newErrors.address = 'L\'adresse doit contenir au moins 5 caractères.';
      valid = false;
    }

    const phoneRegex = /^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/;
    if (!phoneRegex.test(formData.phone)) {
      newErrors.phone = 'Veuillez entrer un numéro de téléphone français valide (ex: 0601020304).';
      valid = false;
    }

    if (formData.card.length !== 16 || !(/^\d+$/.test(formData.card))) {
      newErrors.card = 'Le numéro de carte doit être composé de 16 chiffres.';
      valid = false;
    }

    if (!Number.isInteger(parseInt(formData.crypto)) || formData.crypto.length !== 3) {
      newErrors.crypto = 'Le cryptogramme visuel doit être un entier composé de trois chiffres.';
      valid = false;
    }
    const expiryRegex = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/
    if (!expiryRegex.test(formData.expiry)) {
      newErrors.expiry = 'Veuillez entrer une date d\'expiration valide (ex: 10/26).';
      valid = false;
    }

    if (!luhnValidation(formData.card)) {
      newErrors.card = 'Le numéro de carte ne respecte pas la formule de Luhn.';
      valid = false;
    }

    if (!valid) {
      setErrors(newErrors);
      return;
    }

    try {
      const response = await fetch('http://localhost:8000/api/users', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/ld+json',
        },
        body: JSON.stringify(formData),
      });

      if (response.ok) {
        setMessage('Utilisateur créé avec succès !');
      } else if (response.status === 409) {
        setMessage('Adresse e-mail déjà enregistrée.');
      } else {
        setMessage('Une erreur s\'est produite. Veuillez réessayer.');
      }
    } catch (error) {
      setMessage('Une erreur s\'est produite. Veuillez réessayer.');
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 className="text-2xl font-bold mb-6 text-center">Inscription</h2>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-gray-700">Email:</label>
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.email && <p className="text-red-500">{errors.email}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Mot de passe:</label>
            <input
              type="password"
              name="password"
              value={formData.password}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
          </div>
          <div>
            <label className="block text-gray-700">Nom:</label>
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.name && <p className="text-red-500">{errors.name}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Prénom:</label>
            <input
              type="text"
              name="firstname"
              value={formData.firstname}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.firstname && <p className="text-red-500">{errors.firstname}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Adresse:</label>
            <input
              type="text"
              name="address"
              value={formData.address}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.address && <p className="text-red-500">{errors.address}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Téléphone:</label>
            <input
              type="text"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.phone && <p className="text-red-500">{errors.phone}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Numéro de carte:</label>
            <input
              type="text"
              name="card"
              value={formData.card}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.card && <p className="text-red-500">{errors.card}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Cryptogramme visuel:</label>
            <input
              type="text"
              name="crypto"
              value={formData.crypto}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.crypto && <p className="text-red-500">{errors.crypto}</p>}
          </div>
          <div>
            <label className="block text-gray-700">Date d'expiration:</label>
            <input
              type="text"
              name="expiry"
              value={formData.expiry}
              onChange={handleChange}
              required
              className="w-full px-4 py-2 border rounded"
            />
            {errors.expiry && <p className="text-red-500">{errors.expiry}</p>}
          </div>
          <button
            type="submit"
            className="mt-6 bg-blue-500 text-white px-4 py-2 rounded"
          >
            S'inscrire
          </button>
        </form>
        {message && <p className="text-green-500 mt-4 text-center">{message}</p>}
        <div className="text-center mt-4">
          <Link href="/login">
            <p className="text-blue-500 hover:underline">Déjà un compte ? Connectez-vous</p>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default SignUpForm;
