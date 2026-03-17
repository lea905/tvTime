export default defineEventHandler(async (event) => {
  const id = event.context.params?.id;
  if (!id) throw createError({ statusCode: 400, message: 'Missing ID' });

  const movie = await prisma.movie.findUnique({
    where: { id: parseInt(id) }
  });

  if (!movie) throw createError({ statusCode: 404, message: 'Movie not found' });

  // Transform to match frontend expectations if needed
  return {
    ...movie,
    description: movie.resume, // Map resume to description
    rating: movie.popularity, // Map popularity to rating if needed, or use a real rating
    duration: 120 // Placeholder or add to schema
  };
});
