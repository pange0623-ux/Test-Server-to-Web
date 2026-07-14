import { setupAdminUser } from '@/app/actions/setup-admin'

export async function POST() {
  try {
    const result = await setupAdminUser()
    return Response.json(result)
  } catch (error) {
    return Response.json(
      { success: false, error: error instanceof Error ? error.message : 'Unknown error' },
      { status: 500 }
    )
  }
}
