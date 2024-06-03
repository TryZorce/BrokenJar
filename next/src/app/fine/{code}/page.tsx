'use client';
import React, { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';

const MovieDetail = () => {
    const { code } = useParams();
    const [movieDetails, setMovieDetails] = useState<any>(null);

    useEffect(() => {
       

    }, [code]);


    return (
        <div className="container mx-auto p-6">
            {movieDetails ? (
                <div className="max-w-xl mx-auto">
                    <h2 className="text-3xl font-bold mb-4">{movieDetails.title}</h2>
                    <p className="mb-4">{movieDetails.overview}</p>
                    <div className="mb-4">
                        <p className=" mb-2">Amende numéro : {fine.code}</p>
                        <p className=" mb-2">Raison de l'amende : {fine.name}</p>
                        <p className=" mb-2">Description : {fine.description}</p>
                        <p className=" mb-2">Prix : {fine.value}</p>
                    </div>
                </div>
            ) : (
                <p className="text-center">Chargement...</p>
            )}
        </div>
    );
};

export default MovieDetail;
