import { auth } from '@/lib/auth'
import { headers } from 'next/headers'
import { redirect } from 'next/navigation'
import { DeviceDetailClient } from '@/components/pages/device-detail-client'

export const metadata = {
  title: 'Device Details - Device Monitor',
  description: 'View device details and information',
}

export default async function DeviceDetailPage({
  params,
}: {
  params: { id: string }
}) {
  const session = await auth.api.getSession({ headers: await headers() })

  if (!session?.user) {
    redirect('/sign-in')
  }

  return <DeviceDetailClient deviceId={params.id} user={session.user} />
}
