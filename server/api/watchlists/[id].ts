// server/api/watchlists/[id].ts
export default defineEventHandler(async (event) => {
    const idParam = getRouterParam(event, 'id')
    const id = idParam ? parseInt(idParam) : undefined
    const method = event.method
    const user = await prisma.user.findFirst() // À remplacer par ton auth plus tard

    if (!user) throw createError({ statusCode: 401 })
    if (!id) throw createError({ statusCode: 400, message: 'ID invalide' })

    // MODIFIER UNE LISTE
    if (method === 'PUT' || method === 'PATCH') {
        const body = await readBody(event)
        return await prisma.watchList.update({
            where: { id: id, userId: user.id },
            data: {
                title: body.title,
                description: body.description
            }
        })
    }

    // SUPPRIMER UNE LISTE
    if (method === 'DELETE') {
        await prisma.watchList.delete({
            where: { id: id, userId: user.id }
        })
        return { message: 'Supprimé avec succès' }
    }
})