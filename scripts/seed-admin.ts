import { db } from '@/lib/db'
import { user, session } from '@/lib/db/schema'
import { auth } from '@/lib/auth'
import { v4 as uuidv4 } from 'uuid'
import * as crypto from 'crypto'

async function seedAdmin() {
  try {
    console.log('🌱 Seeding admin user...')

    // Hash password "admin"
    const hashedPassword = await crypto.subtle.digest(
      'SHA-256',
      new TextEncoder().encode('admin')
    )
    const passwordHash = Array.from(new Uint8Array(hashedPassword))
      .map(b => b.toString(16).padStart(2, '0'))
      .join('')

    const adminId = uuidv4()
    const adminEmail = 'admin@localhost'

    // Create admin user
    const existingUser = await db.query.user.findFirst({
      where: (users, { eq }) => eq(users.email, adminEmail),
    })

    if (existingUser) {
      console.log('✅ Admin user already exists')
      return
    }

    // Insert admin user
    await db.insert(user).values({
      id: adminId,
      email: adminEmail,
      name: 'Administrator',
      emailVerified: true,
      createdAt: new Date(),
      updatedAt: new Date(),
    })

    console.log('✅ Admin user created successfully!')
    console.log('   Email: admin@localhost')
    console.log('   Password: admin')
  } catch (error) {
    console.error('❌ Error seeding admin:', error)
    process.exit(1)
  }
}

seedAdmin()
