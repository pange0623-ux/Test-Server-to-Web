'use server'

import crypto from 'crypto'
import bcryptjs from 'bcryptjs'
import { db } from '@/lib/db'
import { user, account } from '@/lib/db/schema'
import { eq } from 'drizzle-orm'

export async function setupAdminUser() {
  try {
    // Check if admin already exists
    const adminEmail = 'admin@app.local'
    const existingAdmin = await db
      .select()
      .from(user)
      .where(eq(user.email, adminEmail))

    if (existingAdmin.length > 0) {
      return {
        success: false,
        message: 'Admin user already exists',
        email: adminEmail,
        password: 'admin1234',
      }
    }

    // Create admin user
    const userId = crypto.randomUUID()
    const accountId = crypto.randomUUID()

    await db.insert(user).values({
      id: userId,
      name: 'Administrator',
      email: adminEmail,
      emailVerified: true,
    })

    // Hash password with bcrypt (minimum 8 characters required by Better Auth)
    const hashedPassword = await bcryptjs.hash('admin1234', 10)

    await db.insert(account).values({
      id: accountId,
      userId,
      type: 'email',
      provider: 'email',
      providerAccountId: adminEmail,
      password: hashedPassword,
    })

    return {
      success: true,
      message: 'Admin user created successfully',
      email: adminEmail,
      password: 'admin1234',
    }
  } catch (error) {
    console.error('Error setting up admin:', error)
    return {
      success: false,
      message: 'Failed to create admin user',
      error: error instanceof Error ? error.message : 'Unknown error',
    }
  }
}
