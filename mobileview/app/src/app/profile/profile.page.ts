import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonList, IonItem, IonLabel } from '@ionic/angular/standalone';
import { AuthService, User } from '../auth.service';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [CommonModule, IonHeader, IonToolbar, IonTitle, IonContent, IonButton, IonList, IonItem, IonLabel],
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss']
})
export class ProfilePage {
  user: User | null = null;

  constructor(private auth: AuthService, private router: Router) {
    this.auth.user$.subscribe((u) => (this.user = u));
  }

  getRoleNames(): string {
    if (!this.user || !this.user.roles) {
      return 'No roles assigned';
    }
    return this.user.roles.map(r => r.title).join(', ');
  }

  logout() {
    this.auth.logout();
    this.router.navigate(['/login']);
  }
} 