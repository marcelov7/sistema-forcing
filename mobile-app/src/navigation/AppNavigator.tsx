import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createStackNavigator } from '@react-navigation/stack';
import { Ionicons } from '@expo/vector-icons';

// Screens
import DashboardScreen from '../screens/DashboardScreen';
import ForcingListScreen from '../screens/ForcingListScreen';
import ForcingCreateScreen from '../screens/ForcingCreateScreen';
import ForcingDetailScreen from '../screens/ForcingDetailScreen';
import ProfileScreen from '../screens/ProfileScreen';

const Tab = createBottomTabNavigator();
const Stack = createStackNavigator();

function ForcingStack() {
  return (
    <Stack.Navigator>
      <Stack.Screen 
        name="ForcingList" 
        component={ForcingListScreen}
        options={{ title: 'Forcings' }}
      />
      <Stack.Screen 
        name="ForcingDetail" 
        component={ForcingDetailScreen}
        options={{ title: 'Detalhes' }}
      />
      <Stack.Screen 
        name="ForcingCreate" 
        component={ForcingCreateScreen}
        options={{ title: 'Novo Forcing' }}
      />
    </Stack.Navigator>
  );
}

export function AppNavigator() {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        tabBarIcon: ({ focused, color, size }) => {
          let iconName: keyof typeof Ionicons.glyphMap;

          if (route.name === 'Dashboard') {
            iconName = focused ? 'home' : 'home-outline';
          } else if (route.name === 'Forcings') {
            iconName = focused ? 'list' : 'list-outline';
          } else if (route.name === 'Profile') {
            iconName = focused ? 'person' : 'person-outline';
          } else {
            iconName = 'help-outline';
          }

          return <Ionicons name={iconName} size={size} color={color} />;
        },
        tabBarActiveTintColor: '#007AFF',
        tabBarInactiveTintColor: 'gray',
        headerShown: false,
      })}
    >
      <Tab.Screen 
        name="Dashboard" 
        component={DashboardScreen}
        options={{ title: 'Dashboard' }}
      />
      <Tab.Screen 
        name="Forcings" 
        component={ForcingStack}
        options={{ title: 'Forcings' }}
      />
      <Tab.Screen 
        name="Profile" 
        component={ProfileScreen}
        options={{ title: 'Perfil' }}
      />
    </Tab.Navigator>
  );
}

